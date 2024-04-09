<?php

namespace src\Models;

use src\Commons\Model;

class PostModel extends Model
{
    protected ?string $table = 'articles';


    function getAllPost($limit = '')
    {
        if ($limit || is_numeric($limit) || $limit == 0) {
            $limit = "LIMIT " . intval($limit);
        }
        $sql = "SELECT a.*,c.name AS category_name,u.name AS author
        FROM articles a
        JOIN categories c 
        ON a.category_id=c.id
        JOIN users u
        ON a.user_id = u.id
        ORDER BY id DESC
        $limit";
        return $this->query($sql)->fetchAll();
    }

    function getSinglePost($slug)
    {
        $sql = "SELECT a.*,c.name AS category,u.name AS username
        FROM articles a
        JOIN categories c 
        ON a.category_id=c.id
        JOIN users u
        ON a.user_id = u.id
        WHERE a.slug = :slug";
        return $this->query($sql, [':slug' => $slug])->fetch();
    }

    private function getPostView($article_id)
    {
        $sql = "SELECT * 
        FROM article_views 
        WHERE article_id = :article_id AND DATE(last_viewed) = CURDATE() 
        ORDER BY last_viewed DESC";
        return $this->query($sql, [':article_id' => $article_id])->fetch();
    }

    function insertView($article_id)
    {
        $postView = $this->getPostView($article_id);

        if ($postView) {
            $this->query("UPDATE article_views SET view_count = view_count + 1 WHERE article_id = :article_id",
                [
                    ':article_id' => $article_id
                ]);
        } else {
            $sql = "INSERT INTO article_views (article_id) VALUES 
                                      (:article_id)";
            $this->query($sql, [
                ':article_id' => $article_id
            ]);
        }
    }

    function getPostPopular($limit = '')
    {
        if ($limit || is_numeric($limit) || $limit == 0) {
            $limit = "LIMIT " . intval($limit);
        }
        $sql = "SELECT a.id,a.title,a.image,a.summary,a.create_at,a.slug,a.content,av.last_viewed,c.name AS category_name,u.name AS author,DATE(av.Last_viewed) AS view_day,view_sum
        FROM (
            SELECT *,MAX(Last_viewed) AS lastest,SUM(view_count) as view_sum
            FROM article_views
            GROUP BY article_id
        ) av
        JOIN articles a ON a.id = av.article_id
        JOIN categories c ON a.category_id=c.id 
        JOIN users u ON a.user_id = u.id
        ORDER BY view_sum DESC
        $limit
        ";
        return $this->query($sql)->fetchAll();
    }

    public function getPostTrending($limit = '')
    {
        if ($limit || is_numeric($limit) || $limit == 0) {
            $limit = "LIMIT " . intval($limit);
        }
        $sql = "SELECT a.id,a.title,a.image,a.summary,a.content,a.create_at,a.slug,av.last_viewed,c.name AS category_name,u.name AS author,DATE(av.Last_viewed) AS view_day,view_sum
        FROM (
            SELECT *,MAX(Last_viewed) AS lastest,SUM(view_count) as view_sum
            FROM article_views
            WHERE DATE(Last_viewed) >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY article_id
        ) av
        JOIN articles a ON a.id = av.article_id
        JOIN categories c ON a.category_id=c.id 
        JOIN users u ON a.user_id = u.id
        ORDER BY view_sum DESC
        $limit
        ";
        return $this->query($sql)->fetchAll();
    }

    function getAllPostByCategory($slug, $startLimit, $limit)
    {
        $sql = "SELECT a.*,c.name AS category,u.name AS username,c.slug AS c_slug
        FROM articles a
        JOIN categories c 
        ON a.category_id=c.id
        JOIN users u
        ON a.user_id = u.id
        WHERE c.slug = :slug
        LIMIT " . intval($startLimit) . ", " . intval($limit) . "";
        return $this->query($sql, [
            ':slug' => $slug
        ])->fetchAll();
    }

    function getAllPostByKeyword($keyword, $startLimit, $limit)
    {
        $sql = "SELECT a.*,c.name AS category,u.name AS username,c.slug AS c_slug
        FROM articles a
        JOIN categories c 
        ON a.category_id=c.id
        JOIN users u
        ON a.user_id = u.id
        WHERE a.title LIKE :keyword
        ORDER BY a.id DESC
        LIMIT " . intval($startLimit) . ", " . intval($limit) . "";
        return $this->query($sql, [
            ':keyword' => "%$keyword%"
        ])->fetchAll();
    }

    function viewStatistics($from, $to, $type='DATE')
    {
        $sql = "WITH RECURSIVE DateRange AS (
          SELECT DATE('$from') AS date
          UNION ALL
          SELECT DATE_ADD(date, INTERVAL 1 DAY)
          FROM DateRange
          WHERE DATE_ADD(date, INTERVAL 1 DAY) <= DATE('$to')
        )
        SELECT 	COALESCE(SUM(av.view_count), 0) AS view_count, DateRange.Date AS Date
        FROM article_views av
        RIGHT JOIN DateRange ON DATE(av.Last_viewed) = DATE(DateRange.Date)
        GROUP BY DATE(DateRange.Date)
        ORDER BY DATE( DateRange.Date)
";
        return $this->query($sql)->fetchAll();
    }

    function viewStatisticsByIdPost($from, $to, $post_id, $type='DATE')
    {
        $sql = "WITH RECURSIVE DateRange AS (
          SELECT DATE('$from') AS date
          UNION ALL
          SELECT DATE_ADD(date, INTERVAL 1 DAY)
          FROM DateRange
          WHERE DATE_ADD(date, INTERVAL 1 DAY) <= DATE('$to')
        )
        SELECT COALESCE(SUM(av.view_count), 0) AS view_count, DateRange.Date AS Date, av.article_id as post_id
        FROM (
        	SELECT * FROM article_views
            WHERE article_views.article_id = $post_id
        ) AS av
        RIGHT JOIN DateRange ON DATE(av.Last_viewed) = DATE(DateRange.Date)
        GROUP BY DATE(DateRange.Date)
        ORDER BY DATE( DateRange.Date)
";
        return $this->query($sql)->fetchAll();
    }



}