package com.megasoft.entangle;

import com.megasoft.entangle.megafragments.TangleFragment;
import com.megasoft.entangle.views.RoundedImageView;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.support.v4.app.Fragment;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;

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
	 * The view of the request 
	 */
	private View view;

	/**
	 * The text of the requester button
	 */
	private String requesterString;

	/**
	 * The request button
	 */
	private TextView request;

	/**
	 * The requester button
	 */
	private TextView requester;
	
	/**
	 * The requester avatar
	 */
	private RoundedImageView requesterAvatar;

	/**
	 * The price text
	 */
	private String price;

	/**
	 * The offers count text
	 */
	private String offersCount;
	
	private TangleFragment parent;

	private Activity activity;

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
			int requesterId, String requestString, String requesterString, String price, String offersCount, TangleFragment parent) {
		StreamRequestFragment fragment = new StreamRequestFragment();
		fragment.setRequestId(requestId);
		fragment.setParent(parent);
		fragment.setRequesterId(requesterId);
		fragment.setRequestButtonText(requestString);
		fragment.setRequesterButtonText(requesterString);
		fragment.setPrice(price);
		fragment.setOffersCount(offersCount);
		return fragment;
	}

	/**
	 * This method is used to setup and return the view of the fragment
	 */
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState) {
		view = inflater.inflate(R.layout.fragment_stream_request,
				container, false);
		request = (TextView) view.findViewById(R.id.requestDescription);
		requesterAvatar = (RoundedImageView) view.findViewById(R.id.requesterAvatar);
		requester = (TextView) view.findViewById(R.id.requesterName);
		setRequestRedirection();
		setRequesterRedirection();
		TextView priceView = (TextView) view.findViewById(R.id.requestPrice);
		priceView.setText(price);
		TextView offersCountView = (TextView) view.findViewById(R.id.requestOffersCount);
		offersCountView.setText(offersCount);
		
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
	 * This method is used to set the text of the price
	 * 
	 * @param text
	 *            , text to be written in the request button
	 */
	private void setPrice(String text) {
		price = text;
	}
	
	/**
	 * This method is used to set the text of the offers count
	 * 
	 * @param text
	 *            , text to be written in the request button
	 */
	private void setOffersCount(String text) {
		offersCount = text;
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
		requesterAvatar.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity().getBaseContext(),
						GeneralProfileActivity.class);
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
		request.setText(getRequestString());
		view.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity().getBaseContext(),
						RequestActivity.class);
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
	
	public void setParent(TangleFragment parent) {
		this.parent = parent;
	}

	
	
}
