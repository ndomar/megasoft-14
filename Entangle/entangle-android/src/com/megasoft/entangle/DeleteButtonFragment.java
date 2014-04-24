package com.megasoft.entangle;

import android.app.Fragment;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.DeleteRequest;

public class DeleteButtonFragment extends Fragment {
	
	private String sessionId;
	private int resourceId;
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

	public int getResourceId() {
		return resourceId;
	}

	public void setResourceId(int resourceId) {
		this.resourceId = resourceId;
	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
		SharedPreferences settings = getActivity().getSharedPreferences(Config.SETTING, 0);
		setSessionId(settings.getString(Config.SESSION_ID, ""));
		
		setResourceId(getArguments().getInt(Config.REQUEST_ID));
		setResourceType(getArguments().getString(Config.RESOURCE_TYPE));
		
		View view = inflater.inflate(R.layout.delete_button_fragment, container, false);
		
		Button deleteButton = (Button) view.findViewById(R.id.deleteButton);
		deleteButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View arg0) {
				String url = Config.API_BASE_URL;
				
				if(getResourceType().equals(Config.REQUEST_TYPE)){
					url += "/request/" + getResourceId();
				}
				else if(getResourceType().equals(Config.OFFER_TYPE)){
					url += "/offer/" + getResourceId();
				}
				
				DeleteRequest deleteRequest = new DeleteRequest(url){
					protected void onPostExecute(String res) {
						String message = "Sorry, there are problems in deleting. Please, try again later";
						if (!this.hasError() && res != null) {
							message = "Deleted!";
						}
						toasterShow(message);
					}
				};
			}
		});
		
		return view;
	}
	
	public void toasterShow(String message){
		Toast.makeText(getActivity().getBaseContext(),
				message,
				Toast.LENGTH_LONG).show();
	}
}
