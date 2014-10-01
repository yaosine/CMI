<?php

/**
 * Common Module Interface
 * 
 * Memcache操作类
 *
 * @copyright (c) 2009-2014 Yaosine.com
 * @author yaosine@gmail.com
 *
 */

class cmi_mem extends cmi{

	var $mem;
	var $client_type = 'Memcache';//Memcache or Memcached
	var $expiration = 86400;
	var $prefix = 'cmi';
	var $flag = 0;
	var $local_cache = array();
    var $errors = array();

	function cmi_mem(){
		$this->set_mem($this->client_type);
	}

	function set_mem($client_type=''){
		if($client_type) $this->client_type = class_exists($client_type) ? $client_type : false;
		else $this->client_type = class_exists('Memcache') ? "Memcache" : (class_exists('Memcached') ? "Memcached" : FALSE);
		if($this->client_type){
            // 判断引入类型
            switch($this->client_type) {
                case 'Memcached':
                    $this->mem = new Memcached();
                    break;
                case 'Memcache':
                    $this->mem = new Memcache();
                    break;
            }  
        }
        else{
            echo 'ERROR: Failed to load Memcached or Memcache Class';
            exit;
        }
        return true;
	}

	/**
	 * 加载指定单元Memcache对象
	 * @param string $unit Memcache单元
	 * @return Memcache对象
	 */
	function load($unit){
		if(!isset($this->$unit)){
			$mem_unit = $this->load_config('mem');
			if(isset($mem_unit[$unit])){
				$this->$unit = $this->connect($mem_unit[$unit]) ? $this : false;
			}
			else return false;
		}
		return $this->$unit;
			
	}

	/**
	 * 加载指定单元Memcached对象
	 * @param string $unit Memcached单元
	 * @return Memcached对象
	 */
	function load_memcached($unit){
		$this->set_mem('Memcached');//使用Memcached
		return $this->load($unit);
			
	}

	/**
     * @Name: connect
     * @param:array server
     * @todu 连接memcache server
     * @return : object
    **/
    function connect($server){
    	if(!is_array($server)) return false;
    	if(isset($server[0])){
			//连接池
			foreach ($server as $key => $conf) {
				if(isset($conf['weight'])){
					if(!$this->mem->addServer($conf['host'], $conf['port'], $conf['weight'])) 
						echo 'ERROR: Could not addServer to the server '.$conf['host'].':'.$conf['port'];
				}
			}
			return true;
		}
		else{
			//单台连接
			if(isset($server['pconnect'])){
				if($server['pconnect'] == 1) $mem = $this->mem->pconnect($server['host'], $server['port']);
				else $mem = $this->mem->pconnect($server['host'], $server['port']);
				if(!$mem) echo 'ERROR: Could not connect to the server '.$server['host'].':'.$server['port'];
				return $mem;
			}
		}
		return false;
    }

    /**
     * @Name: add_server
     * @todu 添加
     * @param:$key key
     * @param:$value 值
     * @param:$expiration 过期时间
     * @return : TRUE or FALSE
    **/
    function add($key = NULL, $value = NULL, $expiration = 0){
        if(is_null($expiration)){
            $expiration = $this->expiration;
        }
        if(is_array($key)){
            foreach($key as $multi){
                if(!isset($multi['expiration']) || $multi['expiration'] == ''){
                    $multi['expiration'] = $this->expiration;
                }
                $this->add($this->key_name($multi['key']), $multi['value'], $multi['expiration']);
            }
        }else{
            $this->local_cache[$this->key_name($key)] = $value;
            switch($this->client_type){
                case 'Memcache':
                    $add_status = $this->mem->add($this->key_name($key), $value, $this->flag, $expiration);
                    break;
                default:
                case 'Memcached':
                    $add_status = $this->mem->add($this->key_name($key), $value, $expiration);
                    break;
            }
            return $add_status;
        }
    }
     
    /**
     * @Name   与add类似,但服务器有此键值时仍可写入替换
     * @param  $key key
     * @param  $value 值
     * @param  $expiration 过期时间
     * @return TRUE or FALSE
     * add by cheng.yafei
    **/
    function set($key = NULL, $value = NULL, $expiration = NULL){
        if(is_null($expiration)){
            $expiration = $this->expiration;
        }
        if(is_array($key)){
            foreach($key as $multi){
                if(!isset($multi['expiration']) || $multi['expiration'] == ''){
                    $multi['expiration'] = $this->config['config']['expiration'];
                }
                $this->set($this->key_name($multi['key']), $multi['value'], $multi['expiration']);
            }
        }else{
            $this->local_cache[$this->key_name($key)] = $value;
            switch($this->client_type){
                case 'Memcache':
                    $add_status = $this->mem->set($this->key_name($key), $value, $this->flag, $expiration);
                    break;
                case 'Memcached':
                    $add_status = $this->mem->set($this->key_name($key), $value, $expiration);
                    break;
            }
            return $add_status;
        }
    }
     
    /**
     * @Name   get 根据键名获取值
     * @param  $key key
     * @return array OR json object OR string...
     * add by cheng.yafei
    **/
    function get($key = NULL){
        if($this->mem){
            if(isset($this->local_cache[$this->key_name($key)])) {
                return $this->local_cache[$this->key_name($key)];
            }
            if(is_null($key)){
                $this->errors[] = 'The key value cannot be NULL';
                return FALSE;
            }
             
            if(is_array($key)){
                foreach($key as $n=>$k){
                    $key[$n] = $this->key_name($k);
                }
                return $this->mem->getMulti($key);
            }else{
                return $this->mem->get($this->key_name($key));
            }
        }else{
            return FALSE;
        }      
    }
     
    /**
     * @Name   delete
     * @param  $key key
     * @param  $expiration 服务端等待删除该元素的总时间
     * @return true OR false
    **/
    function delete($key, $expiration = NULL){
        if(is_null($key)){
            $this->errors[] = 'The key value cannot be NULL';
            return FALSE;
        }
         
        if(is_null($expiration)){
            $expiration = $this->expiration;
        }
         
        if(is_array($key)){
            foreach($key as $multi){
                $this->delete($multi, $expiration);
            }
        }
        else{
            unset($this->local_cache[$this->key_name($key)]);
            return $this->mem->delete($this->key_name($key), $expiration);
        }
    }
     
    /**
     * @Name   replace
     * @param  $key 要替换的key
     * @param  $value 要替换的value
     * @param  $expiration 到期时间
     * @return none
    **/
    function replace($key = NULL, $value = NULL, $expiration = NULL){
        if(is_null($expiration)){
            $expiration = $this->expiration;
        }
        if(is_array($key)){
            foreach($key as $multi) {
                if(!isset($multi['expiration']) || $multi['expiration'] == ''){
                    $multi['expiration'] = $this->config['config']['expiration'];
                }
                $this->replace($multi['key'], $multi['value'], $multi['expiration']);
            }
        }else{
            $this->local_cache[$this->key_name($key)] = $value;
            switch($this->client_type){
                case 'Memcache':
                    $replace_status = $this->mem->replace($this->key_name($key), $value, $this->flag, $expiration);
                    break;
                case 'Memcached':
                    $replace_status = $this->mem->replace($this->key_name($key), $value, $expiration);
                    break;
            }
            return $replace_status;
        }
    }
     
    /**
     * @Name   replace 清空所有缓存
     * @return none
    **/
    public function flush(){
        return $this->mem->flush();
    }
     
    /**
     * @Name   获取服务器池中所有服务器的版本信息
    **/
    function getversion(){
        return $this->mem->getVersion();
    }
     
     
    /**
     * @Name   获取服务器池的统计信息
    **/
    function getstats($type="items"){
        switch($this->client_type){
            case 'Memcache':
                $stats = $this->mem->getStats($type);
                break;
            default:
            case 'Memcached':
                $stats = $this->mem->getStats();
                break;
        }
        return $stats;
    }
     
    /**
     * @Name: 开启大值自动压缩
     * @param:$tresh 控制多大值进行自动压缩的阈值。
     * @param:$savings 指定经过压缩实际存储的值的压缩率，值必须在0和1之间。默认值0.2表示20%压缩率。
     * @return : true OR false
    **/
    function setcompressthreshold($tresh, $savings=0.2){
        switch($this->client_type){
            case 'Memcache':
                $setcompressthreshold_status = $this->mem->setCompressThreshold($tresh, $savings=0.2);
                break;
            default:
                $setcompressthreshold_status = TRUE;
                break;
        }
        return $setcompressthreshold_status;
    }
     
    /**
     * @Name: 生成md5加密后的唯一键值
     * @param:$key key
     * @return : md5 string
    **/
    function key_name($key){
        return md5(strtolower($this->prefix.$key));
    }
     
    /**
     * @Name: 向已存在元素后追加数据
     * @param:$key key
     * @param:$value value
     * @return : true OR false
    **/
    function append($key = NULL, $value = NULL){
        $this->local_cache[$this->key_name($key)] = $value;
        switch($this->client_type){
            case 'Memcache':
                $append_status = $this->mem->append($this->key_name($key), $value);
                break;
            default:
            case 'Memcached':
                $append_status = $this->mem->append($this->key_name($key), $value);
                break;
        }
        return $append_status;
    }

}

?>
