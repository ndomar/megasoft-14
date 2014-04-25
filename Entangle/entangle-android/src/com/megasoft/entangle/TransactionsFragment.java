package com.megasoft.entangle;

import android.support.v4.app.Fragment;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

public class TransactionsFragment extends Fragment {
	
	TextView requesterView;
	TextView requestView;
	TextView amountView;
	String requester;
	int requesterId;
	String request;
	int requestId;
	int tangleId;
	int amount;
	
	public static TransactionsFragment createInstance(String requester, 
			String request, int amount, int requestId, int requesterId, int tangleId) {
		TransactionsFragment fragment = new TransactionsFragment();
		fragment.setRequesterId(requesterId);
		fragment.setRequester(requester);
		fragment.setRequestId(requestId);
		fragment.setRequestView(request);
		fragment.setTangleId(tangleId);
		fragment.setAmount(amount);
		return fragment;
	}
	
	public int getTangleId() {
		return tangleId;
	}

	public void setTangleId(int tangleId) {
		this.tangleId = tangleId;
	}

	public int getRequesterId() {
		return requesterId;
	}

	public void setRequesterId(int requesterId) {
		this.requesterId = requesterId;
	}
	
	public int getRequestId() {
		return requestId;
	}

	public void setRequestId(int requestId) {
		this.requestId = requestId;
	}

	public String getRequester() {
		return requester;
	}

	public void setRequester(String requester) {
		this.requester = requester;
	}

	public String getRequest() {
		return request;
	}

	public void setRequestView(String request) {
		this.request = request;
	}

	public int getAmount() {
		return amount;
	}

	public void setAmount(int amount) {
		this.amount = amount;
	}

	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState) {
		View view = inflater.inflate(R.layout.fragment_profile,
				container, false);
		requestView = (TextView) view.findViewById(R.id.profile_request);
		setRequestRedirection();
		requesterView = (TextView) view.findViewById(R.id.profile_requester);
		setRequesterRedirection();
		amountView = (TextView) view.findViewById(R.id.profile_request_amount);
		//amountView.setText(amount);

		return view;
	}
	/**
	 * This method is used to set the action of the requester button, in which
	 * it will redirect to the requester profile
	 * @author HebaAamer
	 * @param requester
	 *            , is the requester button
	 */
	private void setRequesterRedirection() {
		requesterView.setText(getRequester());
		requesterView.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent profile = new Intent(getActivity().getBaseContext(),
						ProfileActivity.class);
				profile.putExtra("userId", getRequesterId());
				profile.putExtra("tangleId", getTangleId());
				startActivity(profile);
			}
		});
	}

	/**
	 * This method is used to set the action of the request button, in which it
	 * will redirect to the request page
	 * @author HebaAamer
	 * @param request
	 *            , is the request button
	 */
	private void setRequestRedirection() {
		requestView.setText(getRequest());
		requestView.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity().getBaseContext(),
						RequestActivity.class);
				intent.putExtra("tangleId",getTangleId());
				intent.putExtra("requestId", getRequestId());
				startActivity(intent);
			}
		});
	}
 
	
}
