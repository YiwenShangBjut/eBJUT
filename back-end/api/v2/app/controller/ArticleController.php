<?php

/**
 * Created by PhpStorm.
 * User: Xicheng
 * Date: 2019/4/28
 * Time: 14:55
 */

class ArticleController extends Controller
{
    /**
     * @param $page integer path(0, 1)
     * @param $limit integer path(1, 20)
     *
     * @api {get} /article/list/ Request article list
     * @apiName GetArticleList
     * @apiGroup Articles
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/article/list/
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} page Page number.
     * @apiParam {Integer} limit Item limit per page.
     *
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.article_id Article id.
     * @apiSuccess (200) {String} data.article_title Article title.
     * @apiSuccess (200) {Boolean} data.article_is_external Article is external resource.
     * @apiSuccess (200) {String} data.article_external_url Article source link.
     * @apiSuccess (200) {Datetime} data.article_published_date Article published time.
     *
     * @apiError (400) {String} OUT_OF_RANGE Arguments out of range.
     */
    public function ac_list_get($page, $limit)
    {
        if ($limit > 0 && $limit <= 20 && $page > 0) {
            $data = (new ArticlesModel())->getList($page, $limit);
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        } else {
            $this->assignAll(['msg' => 'OUT_OF_RANGE', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @param $article_id integer path(0)
     *
     * @api {get} /article/view/ Request article content
     * @apiName GetArticleContent
     * @apiGroup Articles
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/article/view/
     * @apiVersion 0.1.0
     *
     * @apiParam {Integer} article_id The article id.
     *
     * @apiSuccess (200) {Object[]} data Response payload.
     * @apiSuccess (200) {Integer} data.article_id Article id.
     * @apiSuccess (200) {String} data.article_title Article title.
     * @apiSuccess (200) {Boolean} data.article_is_external Article is external resource.
     * @apiSuccess (200) {String} data.article_external_url Article source link.
     * @apiSuccess (200) {Datetime} data.article_published_date Article published time.
     * @apiSuccess (200) {String} data.article_content Article content.
     *
     * @apiError (404) {String} NOT_FOUND Article not found.
     */
    public function ac_view_get($article_id)
    {
        if (null != $data = (new ArticlesModel())->getArticle($article_id)) {
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $data])->render();
        } else {
            $this->assignAll(['msg' => 'NOT_FOUND', 'code' => 404, 'extra' => ''])->render();
        }
    }

    /**
     * @filter admin canPostArticle
     * @param $article_title string
     * @param $article_external_url string
     * @param $article_content string
     *
     * @api {post} /article/post Post an article
     * @apiName PostNewArticle
     * @apiGroup Articles
     * @apiPermission admin
     * @apiSampleRequest https://dev.iecho.cc/api/article/post/
     * @apiVersion 0.1.0
     *
     * @apiParam {String} user_token User token.
     * @apiParam {String} article_title Article title.
     * @apiParam {String} article_external_url Article source link.
     * @apiParam {String} article_content Article content.
     *
     * @apiSuccess (200) {String} data OK
     *
     * @apiError (400) {String} MISSING_ARGUMENTS Missing arguments.
     *
     * @apiError (502) {String} BAD_GATEWAY Upstream service error.
     */
    public function ac_post_post($article_title, $article_external_url, $article_content)
    {
        // use XOR for single choice
        if (!empty($article_title) && (empty($article_external_url) ^ empty($article_content))) {
            if ((new ArticlesModel())->post($article_title, $article_external_url, $article_content)) {
                $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => ''])->render();
            } else {
                $this->assignAll(['msg' => 'BAD_GATEWAY', 'code' => 502, 'extra' => ''])->render();
            }
        } else {
            $this->assignAll(['msg' => 'MISSING_ARGUMENTS', 'code' => 400, 'extra' => ''])->render();
        }
    }

    /**
     * @api {get} /article/banner Get a banner image
     * @apiName GetBannerImage
     * @apiGroup Articles
     * @apiPermission anyone
     * @apiSampleRequest https://dev.iecho.cc/api/article/banner/
     * @apiVersion 0.1.0
     *
     * @apiSuccess (200) {String} data The image url.
     */
    public function ac_banner_get()
    {
        $bing_url = "https://cn.bing.com";
        $bing_content = file_get_contents($bing_url);
        preg_match_all('/\/(?<img>th\?id=.*?)\&/', $bing_content, $bing_url);
        $bing_url['img'] = array_values(array_unique($bing_url['img']));

        if (sizeof($bing_url['img']) > 1) {
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => "https://cn.bing.com/" . $bing_url['img'][rand(0, sizeof($bing_url['img']) - 1)] . '&w=1080&rs=2'])->render();
        } else {
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => (new BannerModel())->getUrl()])->render();
        }


    }
}