<?php


class Util
{
    /**
     * 生成配置文件模板
     * @param $post
     * @return bool|int
     */
    public function makeEnv($post)
    {
        $environment = file_get_contents(INSTALL_ROOT . '/template/env.php');
        $environment = str_replace('#DB_HOST#', $post['host'], $environment);
        $environment = str_replace('#DB_NAME#', $post['db'], $environment);
        $environment = str_replace('#DB_USER#', $post['username'], $environment);
        $environment = str_replace('#DB_PWD#', $post['password'], $environment);
        $environment = str_replace('#DB_PORT#', $post['port'], $environment);
        $environment = str_replace('#DB_PREFIX#', $post['prefix'], $environment);

        return file_put_contents ( APP_ROOT.'/.env' , $environment);
    }

    /**
     * 生成锁定文件
     * @return bool|int
     */
    public function makeLock()
    {
        return file_put_contents(APP_ROOT . '/install.lock', '');
    }

    /**
     * 加载锁文件
     * @return bool
     */
    public function loadLock()
    {
        return file_exists(APP_ROOT . './install.lock');
    }
}