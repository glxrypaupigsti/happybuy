<?php

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class mMemcache extends Model {

    //put your code here

    private $m;

    public function __construct() {
        global $config;
        parent::__construct();
        if (class_exists('Memcache')) {
            $memcache = new Memcache;
            if ($memcache->connect($config->memcached['host'], $config->memcached['port'])) {
                $this->m = $memcache;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $k
     * @param type $r
     * @return type
     */
    public final function set($k, $r, $exps = false) {
        global $config;
        return $this->m->set($k, $r, MEMCACHE_COMPRESSED, $exps ? $exps : $config->memcached['exps']);
    }

    /**
     * 
     * @param type $k
     * @return type
     */
    public final function get($k) {
        return $this->m->get($k);
    }

}
