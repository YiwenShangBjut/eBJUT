<?php

/**
 * Created by PhpStorm.
 * User: xinyun
 * Date: 2019/4/25
 * Time: 14:54
 */

class BooksCategoriesModel extends Model
{
    protected $_column = [
        'book_category_id' => ['int(11)'],
        'book_category_name' => ['varchar(255)', 'NOT NULL'],
    ];

    protected $_pk = ['book_category_id']; // Primary Key
    protected $_ai = 'book_cid';  // Auto Increment

    /**
     * @param $book_category_name
     * @return bool
     */
    public function addCategory($book_category_name)
    {
        if($this->add(["book_category_name" => $book_category_name]) > 0)
            return true;
        return false;
    }

    /**
     * @param $book_category_name
     * @param $book_category_id
     * @return bool
     */
    public function updateCategory($book_category_name, $book_category_id)
    {
        if($this->where("book_category_id = :id", ["id" => $book_category_id])->update(["book_category_name"=>$book_category_name]) > 0)
            return true;
        return false;
    }

    /**
     * @param $book_category_id
     * @return bool
     */
    public function deleteCategory($book_category_id)
    {
        if($this->where("book_category_id = :id", ["id"=>$book_category_id])->delete() > 0)
            return true;
        return false;

    }

    /**
     * @param $book_category_id
     * @return array
     */
    public function getCategory($book_category_id)
    {
        if($book_category_id == 0)
        {
            return $this->fetchAll();
        }else{
            return $this->where("book_category_id = ?", [$book_category_id])
                ->fetchAll();

        }
    }

}