package com.megasoft.entangle;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;

public class ClosedRequestFragment extends StreamRequestFragment {
	Button reOpenRequest;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		view = inflater.inflate(R.layout.fragment_closed_request, container,
				false);
		setAttributes();
		reOpenRequest = (Button) view.findViewById(R.id.reopenRequestButton);
		reOpenRequest.setOnClickListener(new View.OnClickListener() {
			public void onClick(View v) {
				reOpenRequest();
			}
		});
		return view;
	}

	public static ClosedRequestFragment createInstance(int requestId,
			int requesterId, String requestString, String requesterString,
			String price, String offersCount, int tangleId, String tangleName) {
		ClosedRequestFragment fragment = new ClosedRequestFragment();
		fragment.setFragmentAttributes(requestId, requesterId, requestString,
				requesterString, price, offersCount, tangleId, tangleName);
		return fragment;
	}

	public void reOpenRequest() {

	}
}
