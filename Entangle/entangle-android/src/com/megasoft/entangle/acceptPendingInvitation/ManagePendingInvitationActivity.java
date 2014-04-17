package com.megasoft.entangle.acceptPendingInvitation;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.FragmentTransaction;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.LinearLayout;

import com.megasoft.config.Config;
import com.megasoft.entangle.R;
import com.megasoft.entangle.R.id;
import com.megasoft.entangle.R.layout;
import com.megasoft.entangle.R.menu;
import com.megasoft.requests.GetRequest;

public class ManagePendingInvitationActivity extends Activity {
	
	private int tangleId;

	/**
	 * The preferences instance
	 */
	SharedPreferences settings;
	
	String sessionId;
	
	int pendingInvitationCount;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_manage_pending_invitation);
		tangleId = getIntent().getIntExtra("tangleId", -1);		
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		
	}
	
	@Override
	protected void onStart() {
		super.onStart();
		fetchData();
	}

	private void fetchData() {
		GetRequest request = new GetRequest(Config.API_BASE_URL + "/tangle/"+tangleId+"/pending-invitations"){
			public void onPostExecute(String response) {
				showData(response);
			}
		};
		request.addHeader("X-SESSION-ID", sessionId);
		request.execute();
		
	}
	
	public void showData(String response){
		((LinearLayout)findViewById(R.id.pending_invitation_layout)).removeAllViews();
		JSONObject json = null;

		try {
			json = new JSONObject(response);
			JSONArray jsonArray = json.getJSONArray("pending-invitations");
			FragmentTransaction transaction = getFragmentManager().beginTransaction();
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
	
	public void removeFragment(PendingInvitationFragment fragment){
		getFragmentManager().beginTransaction().remove(fragment).commit();
		pendingInvitationCount--;
		checkEmptyPendingInvitations();
	}
	
	private void checkEmptyPendingInvitations() {
		if(pendingInvitationCount == 0){
			findViewById(R.id.pending_invitation_no_pending).setVisibility(View.VISIBLE);
		}else{
			findViewById(R.id.pending_invitation_no_pending).setVisibility(View.GONE);
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.manage_pending_invitation, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Handle action bar item clicks here. The action bar will
		// automatically handle clicks on the Home/Up button, so long
		// as you specify a parent activity in AndroidManifest.xml.
		int id = item.getItemId();
		if (id == R.id.action_settings) {
			return true;
		}
		return super.onOptionsItemSelected(item);
	}

}