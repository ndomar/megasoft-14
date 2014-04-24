package com.megasoft.entangle;

import android.app.Fragment;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;

import com.megasoft.config.Config;
import com.megasoft.requests.DeleteRequest;

public class DeleteButtonFragment extends Fragment {
	
	private String sessionId;
	private int requestId;
	private String resourceType;
	
	public String getResourceType() {
		return resourceType;
	}

	public void setResourceType(String resourceType) {
		this.resourceType = resourceType;
	}

	public String getSessionId() {
		return sessionId;
	}

	public void setSessionId(String sessionId) {
		this.sessionId = sessionId;
	}

	public int getRequestId() {
		return requestId;
	}

	public void setRequestId(int requestId) {
		this.requestId = requestId;
	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
		SharedPreferences settings = getActivity().getSharedPreferences(Config.SETTING, 0);
		setSessionId(settings.getString(Config.SESSION_ID, ""));
		
		setRequestId(getArguments().getInt(Config.REQUEST_ID));
		
		View view = inflater.inflate(R.layout.delete_button_fragment, container, false);
		
		Button deleteButton = (Button) view.findViewById(R.id.deleteButton);
		deleteButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View arg0) {
				DeleteRequest deleteRequest = new DeleteRequest(Config.API_BASE_URL + "");
			}
		});
		
		return view;
	}
}
