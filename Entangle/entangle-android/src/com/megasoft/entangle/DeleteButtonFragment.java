package com.megasoft.entangle;

import android.app.AlertDialog;
import android.app.Fragment;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.Toast; 

import com.megasoft.config.Config; 
import com.megasoft.requests.DeleteRequest;

/*
 * Fragment for a button to delete a request or an offer
 * @author Omar ElAzazy
 */
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
		
		setResourceId(getArguments().getInt(Config.REQUEST_ID, -1));
		setResourceType(getArguments().getString(Config.RESOURCE_TYPE));
		
		if(getResourceId() == -1 || getResourceType() == null || getSessionId() == null){
			toasterShow("Sorry, there are problems in deleting. Please, try again later");
			Intent intent = new Intent(getActivity().getBaseContext(), MainActivity.class);
			startActivity(intent);
			getActivity().finish();
		}
		
		View view = inflater.inflate(R.layout.delete_button_fragment, container, false);
		
		Button deleteButton = (Button) view.findViewById(R.id.deleteButton);
		deleteButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View arg0) {
				
				final AlertDialog ad = new AlertDialog.Builder(getActivity()).create();
        		ad.setCancelable(false);
        		ad.setMessage("Deleting ...");
        		ad.show();
				
				String url = Config.API_BASE_URL;
				
				if(getResourceType().equals(Config.REQUEST_TYPE)){
					url += "/request/" + getResourceId();
				}
				else if(getResourceType().equals(Config.OFFER_TYPE)){
					url += "/offer/" + getResourceId();
				}
				
				DeleteRequest deleteRequest = new DeleteRequest(url){
					protected void onPostExecute(String res) {
						String message = this.getErrorMessage();
						
						if (!this.hasError() && res != null) {
							message = "Deleted!";
						}
						
						ad.dismiss();
						toasterShow(message);
						
						Intent intent = new Intent(getActivity().getBaseContext(), MainActivity.class);
						startActivity(intent);
						getActivity().finish();
					}
				};
				
				deleteRequest.addHeader(Config.API_SESSION_ID, getSessionId());
				deleteRequest.execute();
			}
		});
		
		return view;
	}
	
	/*
	 * Function to view message on toaster
	 * @author Omar ElAzazy
	 */
	public void toasterShow(String message){
		Toast.makeText(getActivity().getBaseContext(),
				message,
				Toast.LENGTH_LONG).show();
	}
}