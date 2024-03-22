<?php

namespace V17Development\FlarumBlog\Access;

use Flarum\User\User;
use Illuminate\Database\Eloquent\Builder;

class ScopeDiscussionVisibility
{
    /**
     * @param User $actor
     * @param Builder $query
     */
    public function __invoke(User $actor, Builder $query)
    {
        // Hide blogposts which arent published or are still pending approval
        // Writers will have access to the posts if they are still pending for review
        $this->actorid = $actor->id;
        if( !$actor->hasPermission('blog.canApprovePosts') ) {
            $query->whereNotIn('discussions.id', function ($query) {
                return $query
                    ->select('bm.discussion_id')
                    ->from('blog_meta as bm')
                    ->join('discussions as d','d.id', '=', 'bm.discussion_id','inner')
                    ->where('d.user_id', '!=',  $this->actorid)
                    ->where('bm.is_pending_review', 1);
            });
        }
    }
}
