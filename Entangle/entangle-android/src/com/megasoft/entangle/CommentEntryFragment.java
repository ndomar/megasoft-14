package com.megasoft.entangle;

import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

public class CommentEntryFragment extends Fragment {
	
	private View view;
	private String commenter;
	private String comment;
	private String commentDate;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState){
		this.view = inflater.inflate(R.layout.fragment_comment_entry, container,false);
		((TextView)view.findViewById(R.id.comment_content)).setText(comment);
		((TextView)view.findViewById(R.id.comment_date)).setText(commentDate);
		((TextView)view.findViewById(R.id.commenter)).setText(commenter);
		return view;
	}

	public void setCommenter(String commenter) {
		this.commenter = commenter;
	}

	public void setComment(String comment) {
		this.comment = comment;
	}

	public void setCommentDate(String commentDate) {
		this.commentDate = commentDate;
	}
}
