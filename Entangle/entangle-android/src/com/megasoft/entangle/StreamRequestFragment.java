package com.megasoft.entangle;

import android.annotation.SuppressLint;
import android.app.Fragment;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;

@SuppressLint("NewApi")
public class StreamRequestFragment extends Fragment {
	private int requesterId;
	private int requestId;
	Button request;
	Button requester;

	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState) {
		return inflater.inflate(R.layout.activity_stream_request_fragment,
				container, false);
	}

	public void onStart() {
		requester = (Button) getActivity().findViewById(R.id.requesterButton);
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
		request = (Button) getActivity().findViewById(R.id.requestButton);
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

	private int getRequestId() {
		return requestId;
	}

	private int getRequesterId() {
		return requesterId;
	}

	public void setRequesterId(int id) {
		requesterId = id;
	}

	public void setRequestId(int id) {
		requestId = id;
	}

	public void setRequesterButtonText(String text) {
		requester.setText(text);
	}

	public void setRequestButtonText(String text) {
		request.setText(text);
	}

}
