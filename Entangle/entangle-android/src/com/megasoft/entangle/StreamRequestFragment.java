package com.megasoft.entangle;

import android.annotation.SuppressLint;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;

/**
 * This class extends the fragment class and it corresponds to one entry in the
 * requests stream. It consists of two buttons, one represents the request and
 * the other represents the requester.
 * 
 * @author HebaAamer
 * 
 */
@SuppressLint("NewApi")
public class StreamRequestFragment extends Fragment {
	
	private TangleFragment parent;
	
	/**
	 * The id of the requester
	 */
	private int requesterId;

	/**
	 * The id of the request
	 */
	private int requestId;

	/**
	 * The text of the request button
	 */
	private String requestString;

	/**
	 * The text of the requester button
	 */
	private String requesterString;

	/**
	 * The request button
	 */
	private Button request;

	/**
	 * The requester button
	 */
	private Button requester;

	/**
	 * This method is used to create an instance of the StreamRequestFragment
	 * and sets its fields
	 * 
	 * @param requestId
	 *            , is the id of the request
	 * @param requesterId
	 *            , is the id of the requester
	 * @param requestString
	 *            , is the text of the request button
	 * @param requesterString
	 *            , is the text of the requester button
	 * @return an instance of the StreamRequestFragment
	 */
	public static StreamRequestFragment createInstance(int requestId,
			int requesterId, String requestString, String requesterString, TangleFragment parent) {
		StreamRequestFragment fragment = new StreamRequestFragment();
		fragment.setParent(parent);
		fragment.setRequestId(requestId);
		fragment.setRequesterId(requesterId);
		fragment.setRequestButtonText(requestString);
		fragment.setRequesterButtonText(requesterString);
		return fragment;
	}

	/**
	 * This method is used to setup and return the view of the fragment
	 */
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState) {
		View view = inflater.inflate(R.layout.stream_request_fragment,
				container, false);
		request = (Button) view.findViewById(R.id.requestButton);
		setRequestRedirection();
		requester = (Button) view.findViewById(R.id.requesterButton);
		setRequesterRedirection();
		return view;
	}

	/**
	 * This is a getter method that is used to return the id of the request
	 * 
	 * @return request id
	 */
	private int getRequestId() {
		return requestId;
	}

	/**
	 * This is a getter method that is used to return the id of the requester
	 * 
	 * @return requester id
	 */
	private int getRequesterId() {
		return requesterId;
	}

	/**
	 * This is a getter method that is used to return the text of the request
	 * button
	 * 
	 * @return requestString
	 */
	private String getRequestString() {
		return requestString;
	}

	/**
	 * This is a getter method that is used to return the text of the requester
	 * button
	 * 
	 * @return requesterString
	 */
	private String getRequesterString() {
		return requesterString;
	}

	/**
	 * This method is used to set the id of the requester
	 * 
	 * @param id
	 *            , id of the requester
	 */
	private void setRequesterId(int id) {
		requesterId = id;
	}

	/**
	 * This method is used to set the id of the request
	 * 
	 * @param id
	 *            , id of the request
	 */
	private void setRequestId(int id) {
		requestId = id;
	}

	/**
	 * This method is used to set the text of the requester button
	 * 
	 * @param text
	 *            , text to be written in the requester button
	 */
	private void setRequesterButtonText(String text) {
		requesterString = text;
	}
	
	public void setParent(TangleFragment parent) {
		this.parent = parent;
	}

	/**
	 * This method is used to set the text of the request button
	 * 
	 * @param text
	 *            , text to be written in the request button
	 */
	private void setRequestButtonText(String text) {
		requestString = text;
	}

	/**
	 * This method is used to set the action of the requester button, in which
	 * it will redirect to the requester profile
	 * 
	 * @param requester
	 *            , is the requester button
	 */
	private void setRequesterRedirection() {
		requester.setTextSize(16);
		requester.setText(getRequesterString());
		requester.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity().getBaseContext(),
						ProfileActivity.class);
				intent.putExtra("tangleId",
						parent.getTangleId());
				intent.putExtra("tangleName",
						parent.getTangleName());
				intent.putExtra("sessionId",
						parent.getSessionId());
				intent.putExtra("userId", getRequesterId());
				startActivity(intent);
			}
		});
	}

	
	/**
	 * This method is used to set the action of the request button, in which it
	 * will redirect to the request page
	 * 
	 * @param request
	 *            , is the request button
	 */
	private void setRequestRedirection() {
		request.setTextSize(16);
		request.setText(getRequestString());
		request.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity().getBaseContext(),
						ViewRequestActivity.class);
				intent.putExtra("tangleId",
						parent.getTangleId());
				intent.putExtra("tangleName",
						parent.getTangleName());
				intent.putExtra("sessionId",
						parent.getSessionId());
				intent.putExtra("requestId", getRequestId());
				startActivity(intent);
			}
		});
	}

}
