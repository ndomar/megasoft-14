package com.megasoft.entangle;

import android.annotation.SuppressLint;
import android.app.Fragment;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.ViewGroup.LayoutParams;
import android.widget.Button;
import android.widget.LinearLayout;

@SuppressLint("NewApi")
public class StreamRequestFragment extends Fragment {
	// public static int counter = 0;
	// private int num;
	private int requesterId;
	private int requestId;
	private String requestString;
	private String requesterString;
	Button request;
	Button requester;

	public static StreamRequestFragment createInstance(int requestId,
			int requesterId, String requestString, String requesterString) {
		StreamRequestFragment fragment = new StreamRequestFragment();
		fragment.setRequestId(requestId);
		fragment.setRequesterId(requesterId);
		fragment.setRequestButtonText(requestString);
		fragment.setRequesterButtonText(requesterString);
		return fragment;
	}

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

	// @Override
	// public void onSaveInstanceState(Bundle outState) {
	// Log.d("Method", "onSave");
	// }

	private int getRequestId() {
		return requestId;
	}

	private int getRequesterId() {
		return requesterId;
	}

	private String getRequestString() {
		return requestString;
	}

	private String getRequesterString() {
		return requesterString;
	}

	private void setRequesterId(int id) {
		requesterId = id;
	}

	private void setRequestId(int id) {
		requestId = id;
	}

	private void setRequesterButtonText(String text) {
		// requester = (Button)
		// getActivity().findViewById(R.id.requesterButton);
		// requester.setText(text);
		requesterString = text;
	}

	private void setRequestButtonText(String text) {
		// request = (Button) getActivity().findViewById(R.id.requestButton);
		// request.setText(text);
		requestString = text;
	}
	
	private void setRequesterRedirection() {
		requester.setTextSize(16);
		requester.setText(getRequesterString());
		requester.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity().getBaseContext(),
						Profile.class);
				intent.putExtra("tangleId",
						((TangleProfilePage) getActivity()).getTangleId());
				intent.putExtra("tangleName",
						((TangleProfilePage) getActivity()).getTangleName());
				intent.putExtra("sessionId",
						((TangleProfilePage) getActivity()).getSessionId());
				intent.putExtra("requesterId", getRequesterId());
				startActivity(intent);
			}
		});
	}

	private void setRequestRedirection() {
		request.setTextSize(16);
		request.setText(getRequestString());
		request.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View v) {
				Intent intent = new Intent(getActivity().getBaseContext(),
						RequestPage.class);
				intent.putExtra("tangleId",
						((TangleProfilePage) getActivity()).getTangleId());
				intent.putExtra("tangleName",
						((TangleProfilePage) getActivity()).getTangleName());
				intent.putExtra("sessionId",
						((TangleProfilePage) getActivity()).getSessionId());
				intent.putExtra("requestId", getRequestId());
				startActivity(intent);
			}
		});
	}


}
