<?php

use Laracasts\Presenter\PresentableTrait;

class Topic extends \Eloquent 
{
	use PresentableTrait;
	protected $presenter = 'Phphub\Topic\TopicPresenter';

	use SoftDeletingTrait;
    protected $dates = ['deleted_at'];

	// Don't forget to fill this array
	protected $fillable = [
		'title', 
		'body', 
		'user_id', 
		'node_id'
	];

	public function node()
	{
		return $this->belongsTo('Node');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function replies()
	{
		return $this->hasMany('Reply');
	}

	public function getWikiList()
	{
		return $this->where('is_wiki', '=', true)->orderBy('created_at', 'desc')->get();
	}

	public function getRepliesWithLimit($limit = 10)
	{
		return $this->replies()->orderBy('created_at', 'desc')->with('user')->paginate($limit);
	}

	public function getTopicsWithFilter($filter, $limit = 20)
	{
		return $this->applyFilter($filter)->with('user')->paginate($limit);
	}

	public function getNodeTopicsWithFilter($filter, $node_id, $limit = 20)
	{
		return $this->applyFilter($filter)->where('node_id', '=', $node_id)->with('user')->paginate($limit);
	}

	public function applyFilter($filter)
	{
		switch ($filter) {
			case 'noreply':
				return $this->orderBy('reply_count', 'asc')->orderBy('created_at', 'desc');
				break;
			case 'vote':
				return $this->orderBy('vote_count', 'desc');
				break;
			case 'excellent':
				return $this->where('is_excellent', '=', true)->orderBy('created_at', 'desc');
				break;
			case 'recent':
			default:
				return $this->orderBy('created_at', 'desc');
				break;
		}
	}
}