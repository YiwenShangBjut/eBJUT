<?php


class BookController extends Controller
{
    /**
     * @param $isbn string path(0)
     */
    public function ac_externalDetails($isbn)
    {
        $google_url = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn . "&country=US";
        $google_content = file_get_contents($google_url);
        $google_object = json_decode($google_content, true);
        if ($google_object['totalItems'] > 0) {
            $result = [
                'title' => $google_object['items'][0]['volumeInfo']['title'],
                'authors' => $google_object['items'][0]['volumeInfo']['authors'],
                'published_date' => $google_object['items'][0]['volumeInfo']['publishedDate'],
                'page_count' => $google_object['items'][0]['volumeInfo']['pageCount'],
                'description' => $google_object['items'][0]['volumeInfo']['description']
            ];
            $this->assignAll(['msg' => 'OK', 'code' => 200, 'extra' => '', 'data' => $result])->render();
        } else {
            $this->assignAll(['msg' => 'NOT_FOUND', 'code' => 404, 'extra' => ''])->render();
        }
    }
}