<?php

/**
 * Created by PhpStorm.
 * User: Xingren
 * Date: 2019/5/5
 * Time: 9:14
 */
class ForumCategoriesModel extends Model
{
    protected $_column = [
        'category_id' => ['int(11)', 'NOT NULL'],
    ];
    protected $_pk = ['category_id']; // Primary Key

    protected $_ai = 'category_id';   // Auto Increment

    /**
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getForumCategory($page, $limit)
    {
        return $this->order('category_id')
            ->limit($limit, ($page - 1) * $limit)
            ->fetchAll(['category_id', 'category_name']);
    }

    /**
     * @param $category_name
     * @return bool
     */
    public function addForumCategory($category_name)
    {
        if (!empty($category_name) && (!($this->where('category_name = ?')->fetch(['1']) > 0))) {
            $this->add(['category_name' => $category_name]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $category_id
     * @return bool
     */
    public function checkCategoryId($category_id)
    {
        if (!empty($category_id)) {
            $this->where('category_id = ?', [$category_id])->fetch(['1']);
            return true;
        } else {
            return false;
        }
    }
}