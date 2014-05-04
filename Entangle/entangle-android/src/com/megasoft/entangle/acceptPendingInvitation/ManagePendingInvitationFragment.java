package com.megasoft.entangle.acceptPendingInvitation;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentTransaction;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.entangle.R;
import com.megasoft.requests.GetRequest;
/**
 * The Activity that handles the pending tangle invitations.
 * @author MohamedBassem
 *
 */
public class ManagePendingInvitationFragment extends Fragment {
	
	/**
	 * The id of the current tangle
	 */
	private int tangleId;

	/**
	 * The preferences instance
	 */
	SharedPreferences settings;
	
	/**
	 * The session id of the currently logged in user
	 */
	String sessionId;
	
	/**
	 * The number of pending invitations
	 */
	int pendingInvitationCount;

	private View view;
	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
            Bundle savedInstanceState) {
		this.view = inflater.inflate(R.layout.fragment_manage_pending_invitation, container, false);
		tangleId = getArguments().getInt("tangleId", 1);
		this.settings = getActivity().getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		Button button = (Button) view.findViewById(R.id.manage_pending_invitation_refresh);
		button.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				fetchData();
			}
		});
		fetchData();
		return view;
	}
	
	/**
	 * The method responsible for sending the GET request to the endpoint to fetch the invitations
	 * @author MohamedBassem
	 */
	private void fetchData() {
		if(!isNetworkAvailable()){
			showErrorToast();
			return;
		}
		GetRequest request = new GetRequest(Config.API_BASE_URL + "/tangle/"+tangleId+"/pending-invitations"){
			public void onPostExecute(String response) {
				if(this.getStatusCode() == 200){
					showData(response);
				}else{
					showErrorToast();
				}
			}
		};
		request.addHeader("X-SESSION-ID", sessionId);
		request.execute();
		
	}
	
	/**
	 * The method responsible for adding the pending invitation fetched from the api to the layout
	 * @param response , The string containing the JSON response from the API
	 * @author MohamedBassem
	 */
	public void showData(String response){
		((LinearLayout)view.findViewById(R.id.pending_invitation_layout)).removeAllViews();
		JSONObject json = null;

		try {
			json = new JSONObject(response);
			JSONArray jsonArray = json.getJSONArray("pending-invitations");
			FragmentTransaction transaction = getActivity().getSupportFragmentManager().beginTransaction();
			for(int i=0;i<jsonArray.length();i++){
				JSONObject pendingInvitation = jsonArray.getJSONObject(i);
				
				String text = pendingInvitation.getString("inviter") + " invited ";
				if(pendingInvitation.get("invitee") == null){
					text += "the new member " + pendingInvitation.getString("email");
				}else{
					text +=  pendingInvitation.getString("invitee") + " ( " + pendingInvitation.getString("email") + " )";
				}
				
				PendingInvitationFragment requestFragment = PendingInvitationFragment
						.createInstance( pendingInvitation.getInt("id") , text , this );
				transaction.add(R.id.pending_invitation_layout, requestFragment); 
			}
			
			transaction.commit();
			pendingInvitationCount = jsonArray.length();
			checkEmptyPendingInvitations();
		} catch (JSONException e) {
			e.printStackTrace();
		}
		
		
	}
	
	/**
	 * Triggered by the fragment to remove itself from the layout when approved or rejected
	 * @param fragment , the fragment to be removed
	 * @author MohamedBassem
	 */
	public void removeFragment(PendingInvitationFragment fragment){
		getFragmentManager().beginTransaction().remove(fragment).commit();
		pendingInvitationCount--;
		checkEmptyPendingInvitations();
	}
	
	/**
	 * Used to check whether there is any pending invitations , if not , an appropriate message appears
	 * @author MohamedBassem
	 */
	private void checkEmptyPendingInvitations() {
		if(pendingInvitationCount == 0){
			view.findViewById(R.id.pending_invitation_no_pending).setVisibility(View.VISIBLE);
		}else{
			view.findViewById(R.id.pending_invitation_no_pending).setVisibility(View.GONE);
		}
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
		Toast.makeText(getActivity(), "Sorry , Something went wrong.", Toast.LENGTH_SHORT).show();
	}

}