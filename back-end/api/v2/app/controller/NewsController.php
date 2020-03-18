<?php
/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2019/4/11
 * Time: 21:33
 */

class NewsController extends Controller
{
    /**
     * @param $department string path(0)
     * @param $category string(1)
     * @param $page integer path(2)
     * @param $limit integer path(3)
     * @param $after integer path(4)
     *
     * @api {get} /news/list/ Get news list
     * @apiName GetNewsList
     * @apiGroup News
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/news/list/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} department Connected string of departments.
     * @apiParam {String} category Connected string of category.
     * @apiParam {Integer} page Page number
     * @apiParam {Integer} limit Items limit per page.
     * @apiParam {Integer} after The latest news id which was already cached on client.
     *
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} news_id News id.
     * @apiSuccess (200) {String} news_title News title.
     * @apiSuccess (200) {String} news_department News published department.
     * @apiSuccess (200) {Date} news_publish_date News published date.
     * @apiSuccess (200) {Boolean} news_is_external Is external resource.
     * @apiSuccess (200) {String} news_external_url External resource URL.
     * @apiSuccess (200) {String} news_category News category.
     * @apiSuccess (200) {Boolean} news_has_image Is has image resource.
     * @apiSuccess (200) {Boolean} news_has_attachment Is has attachment.
     *
     * @apiError (400) OUT_OF_RANGE Arguments out of range.
     */
    public function ac_list_get($department, $category, $page, $limit, $after)
    {
        if (empty($page)) {
            $page = 1;
        }
        if (empty($limit)) {
            $limit = 20;
        }
        if ($limit > 0 && $limit <= 20 && $page > 0) {
            $data = (new NewsModel())->getList($department, $category, $page, $limit, $after);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        } else {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @param $news_id integer path(0)
     *
     * @api {get} /news/view/ Get news details
     * @apiName GetNewsDetails
     * @apiGroup News
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/news/view/
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} news_id News id.
     *
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} news_id News id.
     * @apiSuccess (200) {String} news_title News title.
     * @apiSuccess (200) {String} news_department News published department.
     * @apiSuccess (200) {Date} news_publish_date News published date.
     * @apiSuccess (200) {Boolean} news_is_external Is external resource.
     * @apiSuccess (200) {String} news_external_url External resource URL.
     * @apiSuccess (200) {String} news_category News category.
     * @apiSuccess (200) {Boolean} news_has_image Is has image resource.
     * @apiSuccess (200) {Boolean} news_has_attachment Is has attachment.
     * @apiSuccess (200) {String} news_content News content.
     *
     * @apiError (404) NOT_FOUND News not found.
     */
    public function ac_view_get($news_id)
    {
        if (null != $data = (new NewsModel())->getContent($news_id)) {
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        } else {
            $this->assignAll(['msg' => 'NOT_FOUND', 'code' => 404, 'extra' => ''])->render();
        }
    }

    /**
     * @api {get} /news/category/ Get news category
     * @apiName GetNewsCategory
     * @apiGroup News
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/news/category/
     * @apiVersion 0.1.0
     *
     * @apiSuccess (200) {Object[]} data Array of strings.
     */
    public function ac_category_get()
    {
        $data = (new NewsModel())->getCategory();
        $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
    }

    /**
     * @api {get} /news/department/ Get news department
     * @apiName GetNewsDepartment
     * @apiGroup News
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/news/department/
     * @apiVersion 0.1.0
     *
     * @apiSuccess (200) {Object[]} data Array of strings.
     */
    public function ac_department_get()
    {
        $data = (new NewsModel())->getDepartment();
        $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
    }
}