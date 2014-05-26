package com.megasoft.entangle;

import android.support.v4.app.Fragment;
import android.content.Context;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.entangle.R;
import com.megasoft.requests.DeleteRequest;
import com.megasoft.requests.PutRequest;

public class PendingInvitationFragment extends Fragment {

	private int pendingInvitationId;
	
	private String text;
	
	private View layout;
	
	/**
	 * The preferences instance
	 */
	SharedPreferences settings;
	
	String sessionId;
	
	ManagePendingInvitationFragment parent;
	
	Button approveButton;
	
	Button rejectButton;
	
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState) {
		
		layout = inflater.inflate(R.layout.pending_invitation_fragment,container, false);
		
		this.settings = getActivity().getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		
		return layout;
	}

	public void onStart() {
		super.onStart();
		approveButton = (Button) layout.findViewById(R.id.pending_invitation_approve);
		approveButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
				if(!isNetworkAvailable()){
					showErrorToast();
					return;
				}
				approveButton.setEnabled(false);
				rejectButton.setEnabled(false);
				PutRequest request = new PutRequest(Config.API_BASE_URL + "/pending-invitation/"+ pendingInvitationId +"/accept"){
					public void onPostExecute(String response) {
						if(this.getStatusCode() == 200){
							Toast.makeText(getActivity().getApplicationContext(), "Approved !",
									Toast.LENGTH_SHORT).show();
							removeFragment();
						}else{
							showErrorToast();
							approveButton.setEnabled(true);
							rejectButton.setEnabled(true);
						}
					}
				};
				request.addHeader("X-SESSION-ID", sessionId);
				request.execute();
			}
		});
		
		rejectButton = (Button) layout.findViewById(R.id.pending_invitation_reject);
		rejectButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
				if(!isNetworkAvailable()){
					showErrorToast();
					return;
				}
				approveButton.setEnabled(false);
				rejectButton.setEnabled(false);
				DeleteRequest request = new DeleteRequest(Config.API_BASE_URL + "/pending-invitation/"+ pendingInvitationId +"/reject"){
					public void onPostExecute(String response) {
						if(this.getStatusCode() == 200){
							Toast.makeText(getActivity().getApplicationContext(), "Rejected !",
									Toast.LENGTH_SHORT).show();
							removeFragment();
						}else{
							showErrorToast();
							approveButton.setEnabled(true);
							rejectButton.setEnabled(true);
						}
					}
				};
				request.addHeader("X-SESSION-ID", sessionId);
				request.execute();
			}
		});
		
		TextView textView = (TextView) layout.findViewById(R.id.pending_invitation_text);
		textView.setText(text);
		

	}
	
	private void removeFragment() {
		parent.removeFragment(this);
	}
	
	public static PendingInvitationFragment createInstance(int pendingInvitationId,String text, ManagePendingInvitationFragment parent) {
		PendingInvitationFragment fragment = new PendingInvitationFragment();
		fragment.setPendingInvitationId(pendingInvitationId);
		fragment.setText(text);
		fragment.setParent(parent);
		return fragment;
	}
	
	/**
	 * Checks the Internet connectivity.
	 * @return true if there is an Internet connection , false otherwise
	 */
	private boolean isNetworkAvailable() {
	    ConnectivityManager connectivityManager 
	          = (ConnectivityManager) getActivity().getSystemService(Context.CONNECTIVITY_SERVICE);
	    NetworkInfo activeNetworkInfo = connectivityManager.getActiveNetworkInfo();
	    return activeNetworkInfo != null && activeNetworkInfo.isConnected();
	}

	/**
	 * Shows a something went wrong toast
	 */
	private void showErrorToast(){
		Toast.makeText(getActivity().getApplicationContext(), "Sorry , Something went wrong.", Toast.LENGTH_SHORT).show();
	}
	
	public void setPendingInvitationId(int pendingInvitationId) {
		this.pendingInvitationId = pendingInvitationId;
	}

	public void setText(String text) {
		this.text = text;
	}
	
	public void setParent(ManagePendingInvitationFragment parent){
		this.parent = parent;
	}

}