package com.megasoft.entangle.acceptPendingInvitation;

import android.app.Fragment;
import android.content.SharedPreferences;
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
	
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState) {
		
		layout = inflater.inflate(R.layout.pending_invitation_fragment,container, false);
		
		this.settings = getActivity().getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		
		return layout;
	}

	public void onStart() {
		super.onStart();
		Button approveButton = (Button) layout.findViewById(R.id.pending_invitation_approve);
		approveButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
				PutRequest request = new PutRequest(Config.API_BASE_URL + "/pending-invitation/"+ pendingInvitationId +"/accept"){
					public void onPostExecute(String response) {
						Toast.makeText(getActivity().getApplicationContext(), "Approved !",
								Toast.LENGTH_LONG).show();
					}
				};
				request.addHeader("X-SESSION-ID", sessionId);
				request.execute();
			}
		});
		
		Button rejectButton = (Button) layout.findViewById(R.id.pending_invitation_reject);
		rejectButton.setOnClickListener(new OnClickListener() {
			
			@Override
			public void onClick(View v) {
				DeleteRequest request = new DeleteRequest(Config.API_BASE_URL + "/pending-invitation/"+ pendingInvitationId +"/reject"){
					public void onPostExecute(String response) {
						Toast.makeText(getActivity().getApplicationContext(), "Rejected !",
								Toast.LENGTH_LONG).show();
					}
				};
				request.addHeader("X-SESSION-ID", sessionId);
				request.execute();
			}
		});
		
		TextView textView = (TextView) layout.findViewById(R.id.pending_invitation_text);
		textView.setText(text);
	}
	
	public static PendingInvitationFragment createInstance(int pendingInvitationId,String text) {
		PendingInvitationFragment fragment = new PendingInvitationFragment();
		fragment.setPendingInvitationId(pendingInvitationId);
		fragment.setText(text);
		return fragment;
	}
	
	public void setPendingInvitationId(int pendingInvitationId) {
		this.pendingInvitationId = pendingInvitationId;
	}

	public void setText(String text) {
		this.text = text;
	}

}