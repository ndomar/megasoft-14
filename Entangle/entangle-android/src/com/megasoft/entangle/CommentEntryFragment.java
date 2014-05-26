package com.megasoft.entangle;

import com.megasoft.entangle.views.RoundedImageView;
import com.megasoft.requests.ImageRequest;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

/**
 * The class responsible for the comment entry fragment
 * @author mohamedbassem
 *
 */
public class CommentEntryFragment extends Fragment {
	
	private View view;
	/**
	 * The commenter name
	 */
	private String commenter;
	
	/**
	 * The comment body
	 */
	private String comment;
	
	/**
	 * The comment date
	 */
	private String commentDate;
	
	/** 
	 * The commenter avatar
	 */
	private com.megasoft.entangle.views.RoundedImageView commenterAvatar;

	/**
	 * The avatar link
	 */
	private String commenterAvatarURL;


	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState){
		this.view = inflater.inflate(R.layout.fragment_comment_entry, container,false);
		((TextView)view.findViewById(R.id.comment_content)).setText(comment);
		((TextView)view.findViewById(R.id.comment_date)).setText(commentDate);
		((TextView)view.findViewById(R.id.commenter)).setText(commenter);
		commenterAvatar = (RoundedImageView) view.findViewById(R.id.commenterAvatar);
		//new ImageRequest(commenterAvatarURL,getActivity().getApplicationContext(),commenterAvatar);
		return view;
	}

	/**
	 * Sets the commenter
	 * @param commenter
	 */
	public void setCommenter(String commenter) {
		this.commenter = commenter;
	}
	
	/**
	 * Sets the avatar url
	 * @param commenterAvatarURL
	 */
	public void setCommenterAvatarURL(String commenterAvatarURL) {
		this.commenterAvatarURL = commenterAvatarURL;
	}

	/**
	 * Sets the comment
	 * @param comment
	 */
	public void setComment(String comment) {
		this.comment = comment;
	}

	/**
	 * Sets the comment date
	 * @param commentDate
	 */
	public void setCommentDate(String commentDate) {
		this.commentDate = commentDate;
	}
}
