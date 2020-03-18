<?php
/**
 * Created by PhpStorm.
 * User: Xicheng
 * Date: 2019/4/11
 * Time: 14:36
 */

class NewsModel extends Model
{
    /**
     * Should manually set unique constraint for
     * UNIQUE(news_title, news_department, news_publish_date)
     * @var array
     */
    protected $_column = [
        'news_id' => ['int(11)', 'NOT NULL'],
        'news_title' => ['varchar(255)', 'NOT NULL'],
        'news_department' => ['varchar(255)', 'NOT NULL'],
        'news_publish_date' => ['date', 'NOT NULL'],
        'news_is_external' => ['tinyint(1)', 'NOT NULL', 'DEFAULT 0'],
        'news_external_url' => ['varchar(255)'],
        'news_category' => ['varchar(255)'],
        'news_has_image' => ['tinyint(1)', 'NOT NULL', 'DEFAULT 0'],
        'news_has_attachment' => ['tinyint(1)', 'NOT NULL', 'DEFAULT 0'],
        'news_content' => ['text']
    ];

    protected $_pk = ['news_id']; // Primary Key

    protected $_ai = 'news_id';   // Auto Increment

    protected $_uk = [['news_title', 'news_publish_date', 'news_department']]; // Unique key


    /**
     * @param string $department
     * @param string $category
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getList($department, $category, $page, $limit, $after)
    {
        $condition = '(TRUE';
        $param = [];

        $department = urldecode($department);
        if (!empty($department)) {
            $department = explode(',', $department);
            $c = 0;
            $flag = false;
            foreach ($department as $d) {
                $param['d' . $c] = trim($d);
                $condition .= ($flag ? ' OR' : ' AND') . ' news_department = :d' . $c++;
                $flag = true;
            }
        }
        $condition .= ')';

        $condition .= ' AND (TRUE';
        $category = urldecode($category);
        if (!empty($category)) {
            $category = explode(',', $category);
            $c = 0;
            $flag = false;
            foreach ($category as $ca) {
                $param['ca' . $c] = trim($ca);
                $condition .= ($flag ? ' OR' : ' AND') . ' news_category = :ca' . $c++;
                $flag = true;
            }
        }
        $condition .= ')';

        $condition .= ' AND news_id > :after';
        $param['after'] = $after;

        return $this->where($condition, $param)
            ->order('news_publish_date DESC')
            ->limit($limit, ($page - 1) * $limit)
            ->fetchAll(['news_id', 'news_title', 'news_department', 'news_publish_date', 'news_is_external', 'news_external_url', 'news_category', 'news_has_image', 'news_has_attachment']);
    }

    /**
     * @param $news_id integer
     * @return mixed
     */
    public function getContent($news_id)
    {
        return $this->where('news_id = ?', [$news_id])
            ->fetch();
    }

    /**
     * @return array
     */
    public function getCategory()
    {
        $res = array();
        $row = $this->fetchall("DISTINCT(news_category)");
        foreach ($row as $i) {
            if (trim($i = $i['news_category']))
                $res[] = $i;
        }
        return $res;
    }

    /**
     * @return array
     */
    public function getDepartment()
    {
        $res = array();
        $row = $this->fetchall("DISTINCT(news_department)");
        foreach ($row as $i) {
            if (trim($i = $i['news_department']))
                $res[] = $i;
        }
        return $res;
    }
}