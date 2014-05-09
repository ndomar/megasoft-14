package com.megasoft.entangle;

import com.megasoft.entangle.views.RoundedImageView;

import android.annotation.SuppressLint;
import android.support.v4.app.Fragment;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

/**
 * This class extends the fragment class and it corresponds to one entry in the
 * requests stream. It consists of two buttons, one represents the request and
 * the other represents the requester.
 * 
 * @author HebaAamer, MohamedFarghal
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
	protected View view;

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

	/**
	 * The id of the current tangle
	 */
	private int tangleId;

	/**
	 * The name of the current tangle
	 */
	private String tangleName;

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
			int requesterId, String requestString, String requesterString,
			String price, String offersCount, int tangleId, String tangleName) {
		StreamRequestFragment fragment = new StreamRequestFragment();
		fragment.setFragmentAttributes(requestId, requesterId, requestString,
				requesterString, price, offersCount, tangleId, tangleName);
		return fragment;
	}

	protected void setFragmentAttributes(int requestId, int requesterId,
			String requestString, String requesterString, String price,
			String offersCount, int tangleId, String tangleName) {
		this.setRequestId(requestId);
		this.setRequesterId(requesterId);
		this.setRequestButtonText(requestString);
		this.setRequesterButtonText(requesterString);
		this.setPrice(price);
		this.setOffersCount(offersCount);
		this.setTangleId(tangleId);
		this.setTangleName(tangleName);
	}

	/**
	 * This method is used to setup and return the view of the fragment
	 */
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState) {
		view = inflater.inflate(R.layout.fragment_stream_request, container,
				false);
		setAttributes();
		return view;
	}

	protected void setAttributes() {
		request = (TextView) view.findViewById(R.id.requestDescription);
		requesterAvatar = (RoundedImageView) view
				.findViewById(R.id.requesterAvatar);
		requester = (TextView) view.findViewById(R.id.requesterName);
		setRequestRedirection();
		setRequesterRedirection();
		TextView priceView = (TextView) view.findViewById(R.id.requestPrice);
		priceView.setText(price);
		TextView offersCountView = (TextView) view
				.findViewById(R.id.requestOffersCount);
		offersCountView.setText(offersCount);
	}

	/**
	 * This is a getter method that is used to return the id of the request
	 * 
	 * @return request id
	 */
	protected int getRequestId() {
		return requestId;
	}

	/**
	 * This is a getter method that is used to return the id of the requester
	 * 
	 * @return requester id
	 */
	protected int getRequesterId() {
		return requesterId;
	}

	/**
	 * This is a getter method that is used to return the text of the request
	 * button
	 * 
	 * @return requestString
	 */
	protected String getRequestString() {
		return requestString;
	}

	/**
	 * This is a getter method that is used to return the text of the requester
	 * button
	 * 
	 * @return requesterString
	 */
	protected String getRequesterString() {
		return requesterString;
	}

	/**
	 * This method is used to set the id of the requester
	 * 
	 * @param id
	 *            , id of the requester
	 */
	protected void setRequesterId(int id) {
		requesterId = id;
	}

	/**
	 * This method is used to set the id of the request
	 * 
	 * @param id
	 *            , id of the request
	 */
	protected void setRequestId(int id) {
		requestId = id;
	}

	/**
	 * This method is used to set the text of the requester button
	 * 
	 * @param text
	 *            , text to be written in the requester button
	 */
	protected void setRequesterButtonText(String text) {
		requesterString = text;
	}

	/**
	 * This method is used to set the text of the request button
	 * 
	 * @param text
	 *            , text to be written in the request button
	 */
	protected void setRequestButtonText(String text) {
		requestString = text;
	}

	/**
	 * This method is used to set the text of the price
	 * 
	 * @param text
	 *            , text to be written in the request button
	 */
	protected void setPrice(String text) {
		price = text;
	}

	/**
	 * This method is used to set the text of the offers count
	 * 
	 * @param text
	 *            , text to be written in the request button
	 */
	protected void setOffersCount(String text) {
		offersCount = text;
	}

	/**
	 * This method is used to set the action of the requester button, in which
	 * it will redirect to the requester profile
	 * 
	 * @param requester
	 *            , is the requester button
	 */
	protected void setRequesterRedirection() {
		requester.setTextSize(16);
		requester.setText(getRequesterString());
		requester.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity().getBaseContext(),
						ProfileActivity.class);
				intent.putExtra("tangleId", getTangleId());
				intent.putExtra("tangleName", getTangleName());
				intent.putExtra("userId", getRequesterId());
				startActivity(intent);
			}
		});
		requesterAvatar.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity().getBaseContext(),
						ProfileActivity.class);
				intent.putExtra("tangleId", getTangleId());
				intent.putExtra("tangleName", getTangleName());
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
	protected void setRequestRedirection() {
		request.setText(getRequestString());
		view.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity().getBaseContext(),
						RequestActivity.class);
				intent.putExtra("tangleId", getTangleId());
				intent.putExtra("tangleName", getTangleName());
				intent.putExtra("requestId", getRequestId());
				startActivity(intent);
			}
		});
	}

	/**
	 * getter method for tangle id
	 * 
	 * @return tangleId
	 */
	protected int getTangleId() {
		return tangleId;
	}

	/**
	 * getter method for tangle name
	 * 
	 * @return name
	 */
	protected String getTangleName() {
		return tangleName;
	}

	/**
	 * setter method for tangle id
	 * 
	 * @param tangleId
	 *            , id of the tangle
	 */
	protected void setTangleId(int tangleId) {
		this.tangleId = tangleId;
	}

	/**
	 * setter method for the tangle name
	 * 
	 * @param tangleName
	 *            , name of the tangle
	 */
	protected void setTangleName(String tangleName) {
		this.tangleName = tangleName;
	}
}
