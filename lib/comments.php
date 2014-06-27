<?php

/**
 * ownCloud - collaboration plugin
 *
 * @authors Dr.J.Akilandeswari, R.Ramki, R.Sasidharan, P.Suresh
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
/**
 * This class manages comments on posts
 */ 

class OC_Collaboration_Comment
{
	/**
	 * @brief Alter the contents of a comment
	 * @param Comment ID
	 * @param Modified content to be presented in the comment
	 * @return date|boolean (Modified time|false)
	 */
	public static function editComment($comment_id, $content)
	{
		try
		{
			\OCP\DB::beginTransaction();
		
			$query = \OCP\DB::prepare('UPDATE `*PREFIX*collaboration_comment` SET `content`=?, `time`=CURRENT_TIMESTAMP WHERE `comment_id`=?');
			$result = $query->execute(array($content, $comment_id));
		
			$query = \OCP\DB::prepare('SELECT `time` FROM `*PREFIX*collaboration_comment` WHERE `comment_id`=?');
			$result = $query->execute(array($comment_id));
		
			$time = NULL;
			if($row = $result->fetchRow())
			{
				$time = $row['time'];
			}
		
			\OCP\DB::commit();
		
			return $time;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
		
	}
	
	/**
	 * @brief Find the number of comments on the given post
	 * @param Post ID
	 * @return int|boolean (count|false)
	 */
	public static function getCommentCount($post_id)
	{
		try
		{
			$query = OCP\DB::prepare('SELECT COUNT(`comment_id`) AS cnt FROM `*PREFIX*collaboration_comment` WHERE `post_id`=?');
			$result = $query->execute(array($post_id));
			$row = $result->fetchRow();
		
			return $row? $row['cnt']: false;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Checks if the comment has been written (created) by the given member
	 * @param Comment ID
	 * @return int|boolean (Comment ID|false)
	 */
	public static function isCommentWrittenByMember($comment_id, $member)
	{
		try
		{
			$query = \OCP\DB::prepare('SELECT `comment_id` FROM `*PREFIX*collaboration_comment` WHERE `comment_id`=? AND `creator`=?');
			$result = $query->execute(array($comment_id, $member));
		
			return ($result->fetchRow());
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Removes the comment from the database
	 * @param Comment ID
	 * @return boolean (true|false)
	 */
	public static function deleteComment($comment_id)
	{
		try
		{
		
			$query = \OCP\DB::prepare('DELETE FROM `*PREFIX*collaboration_comment` WHERE `comment_id`=?');
			$result = $query -> execute(array($comment_id));
		
			return true;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Create a new comment for the post with the given contents
	 * @param Content to be presented within the comment
	 * @param Member who wrote (created) the comment
	 * @param Post ID
	 * @return int|boolean (Comment ID|false)
	 */
	public static function createComment($content, $member, $post_id)
	{
		try
		{
		
			$query = \OCP\DB::prepare('INSERT INTO `*PREFIX*collaboration_comment`(`content`, `creator`, `post_id`, `time`) VALUES(?, ?, ?, CURRENT_TIMESTAMP)');
			$result = $query -> execute(array($content, $member, $post_id));
		
			return OCP\DB::insertid('*PREFIX*collaboration_comment');
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Fetch the details corresponding to the comment
	 * @param Comment ID
	 * @return Array|boolean (Associative array containing the details|false)
	 */
	public static function readComment($comment_id)
	{
		try
		{
			$query = \OCP\DB::prepare('SELECT * FROM `*PREFIX*collaboration_comment` WHERE `comment_id`=?');
			$result = $query -> execute(array($comment_id));
		
			$val = array();
		
			while($row = $result->fetchRow())
			{
				foreach($row as $key => $value)
				{
					$val[$key] = $value;
				}
			}
	
			return $val;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Fetch the list of comments associated with a post
	 * @param Post ID
	 * @return Array|boolean (Array containing the details|false)
	 */
	public static function readComments($post_id)
	{
		try
		{
			$sql = 'SELECT `comment`.`comment_id`, `comment`.`content`, `comment`.`creator`, `comment`.`time`
					FROM `*PREFIX*collaboration_comment` AS comment, `*PREFIX*collaboration_post` AS post
					WHERE `comment`.`post_id`=? AND `comment`.`post_id`=`post`.`post_id`
					ORDER BY `time`';
				
			$query = OCP\DB::prepare($sql);
		
			$result = $query->execute(array($post_id));

			$val = array(array());
		
			for($i = 0; $row = $result->fetchRow(); $i++)
			{
				foreach($row as $key => $value)
				{
					$val[$i][$key] = $value;
				}
			}
	
			return $val;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
}

// Instantiated to load the class
new OC_Collaboration_Comment();
