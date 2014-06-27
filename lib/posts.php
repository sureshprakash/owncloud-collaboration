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
 * This class manages the posts
 */ 
	
class OC_Collaboration_Post
{
	/**
	 * @brief Fetches the list of posts related to a project visible to the member
	 * @param Member whose posts have to be fetched
	 * @param Project whose posts have to be fetched (Default: All projects)
	 * @param The starting post serial number
	 * @param The number of posts to be fetched
	 * @return Array|boolean (Details of the posts|false)
	 */
	public static function readPosts($member='', $project='', $start=0, $count=20)
	{
		try
		{
			$sql = 'SELECT `project`.`title` AS proj_title, `post`.`title` AS title, `post`.`tid`, `post_id`, 
					`content`, `creator`, `type`, `time` 
					 FROM `*PREFIX*collaboration_post` AS post LEFT JOIN `*PREFIX*collaboration_project` 
					 AS project ON (`post`.`pid`=`project`.`pid` AND `project`.`completed`=false)
					 WHERE ((`post_to_all`=true AND (`post`.`pid` IN 
					 	(SELECT `works_on`.`pid` FROM `*PREFIX*collaboration_works_on` AS works_on WHERE `works_on`.`member`=?)))' . 
					 	(($member == '')? '': ' OR (`post`.`post_id` IN 
					 	(SELECT `notification`.`post_id` 
					 	 FROM `*PREFIX*collaboration_notification` AS notification 
					 	 WHERE `visible_to`=?))') . ') ' . (($project == '')? '': ' AND `project`.`title`=?') . '
					 ORDER BY `time` DESC
					 LIMIT ' . $start . ', ' . $count;
			 
			$query = OCP\DB::prepare($sql);

			$args = array();
		
			$args[0] = $member;
		
			$i = 1;
			if($member != '')
			{
				$args[$i++] = $member;
			}
		
			if($project != '')
			{
				$args[$i++] = $project;
			}

			$result = $query->execute($args);

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
	
	/**
	 * @brief Fetches the details related to the post
	 * @param Post ID
	 * @return Array|boolean (Details of the post|false)
	 */
	public static function getPostDetails($post_id)
	{
		try
		{
			$sql = 'SELECT * FROM `*PREFIX*collaboration_post` WHERE `post_id`=?';
		
			$query = OCP\DB::prepare($sql);
		
			$result = $query->execute(array($post_id));

			$val = array();
		
			if($row = $result->fetchRow())
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
	 * @brief Creates a new post with the given title and contents
	 * @param Title of the post
	 * @param Post content
	 * @param Member who created the post
	 * @param Project to which the post corresponds
	 * @param Post type
	 * @param List of members having read access to the post
	 * @param Whether the calling method has already initiated the (database) transaction
	 * @param Task ID, if the post is related to a task
	 * @return int|boolean (Post ID|false)
	 */
	public static function createPost($title, $content, $creator, $pid, $type, $viewers=array(), $inTransaction=false, $tid=NULL)
	{
		try
		{
			$cnt = count($viewers);
		
			if(!isset($viewers) || $cnt == 0)
				$post_to_all = true;
			else
				$post_to_all = false;
			
			if(!$inTransaction && !$post_to_all)
			{
				\OCP\DB::beginTransaction();
			}
		
			$query = \OCP\DB::prepare('INSERT INTO `*PREFIX*collaboration_post`(`title`, `content`, `creator`, `pid`, `type`, `time`, `post_to_all`, `tid`) VALUES(?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?, ?)');
		
			$query->execute(array($title, $content, $creator, $pid, $type, $post_to_all, $tid));
		
			$post_id = OCP\DB::insertid('*PREFIX*collaboration_post');
		
			if(!$post_to_all)
			{
				$query = \OCP\DB::prepare('INSERT INTO `*PREFIX*collaboration_notification`(`post_id`, `visible_to`) VALUES(?, ?)');
			
				for($i = 0; $i < $cnt; $i++)
				{
					$query->execute(array($post_id, $viewers[$i]));
				}
			
				if(!$inTransaction)
				{
					\OCP\DB::commit();
				}
			}
			
			return $post_id;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Checks if the given member has access to the post
	 * @param Post ID
	 * @param Member
	 * @return int|boolean (Post ID|false)
	 */
	public static function isPostAccessibleToMember($post_id, $member)
	{
		try
		{
			$query = \OCP\DB::prepare('SELECT `post_id` 
									   FROM `*PREFIX*collaboration_post` 
									   WHERE `post_id`=? 
									   AND (`post_to_all`=true OR `post_id` IN
									   (SELECT `post_id`
									    FROM `*PREFIX*collaboration_notification`
									    WHERE `visible_to`=?))');
									    
			$result = $query->execute(array($post_id, $member));
		
			return ($result->fetchRow());
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Checks if the post is a notification explicitly given by a member (Custom Post)
	 * @param Post ID
	 * @return int|boolean (Post ID|false)
	 */
	public static function isPostEditable($pid)
	{
		try
		{
			$query = \OCP\DB::prepare('SELECT `post_id` FROM `*PREFIX*collaboration_post` WHERE `post_id`=? AND `type`=?');
			$result = $query->execute(array($pid, 'Custom Post'));
		
			return ($result->fetchRow());
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}

	/**
	 * @brief Delete the given post and the comments on it
	 * @param Post ID
	 * @param Whether the calling method has already initiated the (database) transaction
	 * @return boolean (true|false)
	 */	
	public static function deletePost($post_id, $inTransaction=false)
	{
		try
		{
			if(!$inTransaction)
			{
				\OCP\DB::beginTransaction();
			}
		
			$del_comment = \OCP\DB::prepare('DELETE FROM `*PREFIX*collaboration_comment` WHERE `post_id`=?');
			$query = \OCP\DB::prepare('DELETE FROM `*PREFIX*collaboration_post` WHERE `post_id`=?');
		
			$result = $query->execute(array($post_id));
			$result = $del_comment->execute(array($post_id));
		
			if(!$inTransaction)
			{
				\OCP\DB::commit();
			}
		
			return true;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
	/**
	 * @brief Delete all the posts created by the given member
	 * @param Member whos posts have to be deleted
	 * @param Whether the calling method has already initiated the (database) transaction
	 */
	public static function removePostsByMember($member, $inTransaction=false)
	{
		try
		{
			if(!$inTransaction)
			{
				OCP\DB::beginTransaction();
			}
		
			$del_notification = OCP\DB::prepare('DELETE FROM `*PREFIX*collaboration_notification` WHERE `visible_to`=?');
			$del_comment = OCP\DB::prepare('DELETE FROM `*PREFIX*collaboration_comment` WHERE `creator`=?');
		
			$query = OCP\DB::prepare('SELECT `post_id` FROM `*PREFIX*collaboration_post` WHERE `creator`=?');
			$result = $query->execute(array($member));
		
			while($row = $result->fetchRow())
			{
				self::deletePost($row['post_id'], true);
			}
		
			$del_notification->execute(array($member));
			$del_comment->execute(array($member));
		
			if(!$inTransaction)
			{
				OCP\DB::commit();
			}
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}

	/**
	 * @brief Edit the given post with the given title and content
	 * @param Post ID
	 * @param Title of the post
	 * @param Modified post content
	 * @return boolean (true|false)
	 */	
	public static function editPost($post_id, $title, $content)
	{
		try
		{
			\OCP\DB::beginTransaction();
		
			$query = \OCP\DB::prepare('UPDATE `*PREFIX*collaboration_post` SET `title`=?, `content`=?, `time`=CURRENT_TIMESTAMP WHERE `post_id`=?');
			$del_comment = \OCP\DB::prepare('DELETE FROM `*PREFIX*collaboration_comment` WHERE `post_id`=?');
		
			$result = $query->execute(array($title, $content, $post_id));
			$result = $del_comment->execute(array($post_id));
		
			\OCP\DB::commit();
		
			return true;
		}
		catch(\Exception $e)
		{
			OC_Log::write('collaboration', __METHOD__ . ', Exception: ' . $e->getMessage(), OCP\Util::DEBUG);
			return false;
		}
	}
	
}

// Instantiated to load the class
new OC_Collaboration_Post();
?>
