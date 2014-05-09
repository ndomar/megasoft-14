package com.megasoft.entangle;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

public class ClosedRequestFragment extends StreamRequestFragment {
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		view = inflater.inflate(R.layout.fragment_closed_request, container,
				false);
		setAttributes();
		return view;
	}

	public static ClosedRequestFragment createInstance(int requestId,
			int requesterId, String requestString, String requesterString,
			String price, String offersCount, int tangleId, String tangleName) {
		ClosedRequestFragment fragment = new ClosedRequestFragment();
		fragment.setRequestId(requestId);
		fragment.setRequesterId(requesterId);
		fragment.setRequestButtonText(requestString);
		fragment.setRequesterButtonText(requesterString);
		fragment.setPrice(price);
		fragment.setOffersCount(offersCount);
		fragment.setTangleId(tangleId);
		fragment.setTangleName(tangleName);
		return fragment;
	}

	public void reOpenRequest() {

	}
}
