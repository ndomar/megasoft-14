package com.megasoft.entangle;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;

public class OpenRequestFragment extends StreamRequestFragment {
	Button deleteRequest;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		view = inflater.inflate(R.layout.fragment_open_request, container,
				false);
		setAttributes();
		deleteRequest = (Button) view.findViewById(R.id.deleteRequestButton);
		deleteRequest.setOnClickListener(new View.OnClickListener() {

			public void onClick(View v) {
				deleteRequest();

			}
		});
		return view;
	}

	public static OpenRequestFragment createInstance(int requestId,
			int requesterId, String requestString, String requesterString,
			String price, String offersCount, int tangleId, String tangleName) {
		OpenRequestFragment fragment = new OpenRequestFragment();
		fragment.setFragmentAttributes(requestId, requesterId, requestString,
				requesterString, price, offersCount, tangleId, tangleName);
		return fragment;
	}

	public void deleteRequest() {

	}

}
