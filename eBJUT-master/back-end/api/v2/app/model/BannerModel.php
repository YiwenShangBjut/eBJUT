<?php


class BannerModel extends Model
{
    protected $_column = [
        'banner_id' => ['INT(11)'],
        'banner_url' => ['VARCHAR(255)', 'NOT NULL'],
        'banner_timestamp' => ['TIMESTAMP', 'CURRENT_TIMESTAMP']
    ];

    protected
        $_pk = ['banner_id']; // Primary Key
    protected
        $_ai = 'banner_id';   // Auto Increment

    public function getUrl()
    {
        return $this->limit(1)->fetch(['banner_url'])['banner_url'];
    }
}

?>