<?php

/**
 * Created by PhpStorm.
 * User: Xicheng
 * Date: 2019/4/28
 * Time: 14:21
 */

class ArticlesModel extends Model
{
    protected $_column = [
        'article_id' => ['int(11)', 'NOT NULL'],
        'article_title' => ['varchar(255)', 'NOT NULL'],
        'article_is_external' => ['tinyint(1)', 'NOT NULL'],
        'article_external_url' => ['varchar(255)'],
        'article_content' => ['text'],
        'article_published_date' => ['datetime']
    ];

    protected $_pk = ['article_id']; // Primary Key

    protected $_ai = 'article_id';   // Auto Increment


    /**
     * @param $page
     * @param $limit
     * @return array
     */
    public function getList($page, $limit)
    {
        return $this->order('article_published_date DESC')
            ->limit($limit, ($page - 1) * $limit)
            ->fetchAll(['article_id', 'article_title', 'article_is_external', 'article_external_url', 'article_published_date']);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getArticle($id)
    {
        return $this->where('article_id = ?', [$id])
            ->fetch(['article_id', 'article_title', 'article_is_external', 'article_external_url', 'article_published_date', 'article_content']);
    }

    /**
     * @param $title
     * @param $external_url
     * @param $content
     * @return bool
     */
    public function post($title, $external_url, $content)
    {
        if (empty($external_url)) {
            if (empty($content))
                return false;
            return $this->add(['article_title' => $title, 'article_is_external' => 0, 'user_id' => (new TokensModel())->getUserIdByToken(),'article_content' => $content]) > 0;
        } else {
            return $this->add(['article_title' => $title, 'article_is_external' => 1, 'user_id' => (new TokensModel())->getUserIdByToken(),'article_external_url' => $external_url]) > 0;
        }
    }
}