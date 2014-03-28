package com.megasoft.entangle;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.os.Bundle;
import android.app.Activity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;
import android.support.v4.app.NavUtils;
import android.transition.Visibility;
import android.annotation.TargetApi;
import android.content.SharedPreferences;
import android.os.Build;

public class InviteUsersConfirmations extends Activity {
	
	String response;
	private JSONArray notMembers; 
	private JSONArray entangleMembers;
	private SharedPreferences settings;
	private String sessionId;
	private int tangleId;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_invite_users_confirmations);
		// Show the Up button in the action bar.
		setupActionBar();
		
		this.settings = getSharedPreferences(Config.SETTING,0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		this.tangleId = getIntent().getIntExtra("com.megasoft.entangle.tangleId",-1);
		this.response = getIntent().getStringExtra("com.megasoft.entangle.emails");
		parseResponse();
	}

	/**
	 * Set up the {@link android.app.ActionBar}, if the API is available.
	 */
	@TargetApi(Build.VERSION_CODES.HONEYCOMB)
	private void setupActionBar() {
		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB) {
			getActionBar().setDisplayHomeAsUpEnabled(true);
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.invite_users_confirmations, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch (item.getItemId()) {
		case android.R.id.home:
			// This ID represents the Home or Up button. In the case of this
			// activity, the Up button is shown. Use NavUtils to allow users
			// to navigate up one level in the application structure. For
			// more details, see the Navigation pattern on Android Design:
			//
			// http://developer.android.com/design/patterns/navigation.html#up-vs-back
			//
			NavUtils.navigateUpFromSameTask(this);
			return true;
		}
		return super.onOptionsItemSelected(item);
	}
	
	/**
	 * Parse the received response from the callee intent into the classification
	 * received in the json
	 */
	private void parseResponse() {
		try {
			JSONObject json = new JSONObject(this.response);
			
			this.notMembers = json.getJSONArray("notMembers");
			this.entangleMembers = json.getJSONArray("entangleMembers");
			JSONArray alreadyInTheTangle = json.getJSONArray("alreadyInTheTangle");
			JSONArray invalid = json.getJSONArray("invalid");
			
			if(notMembers.length() != 0){
				LinearLayout notMembersLayout = ((LinearLayout)findViewById(R.id.invite_not_members));
				for(int i=0;i<notMembers.length();i++){
					TextView newView = new TextView(this);
					newView.setText(notMembers.getString(i));
					notMembersLayout.addView(newView);
				}
			}else{
				((LinearLayout)findViewById(R.id.invite_not_members)).setVisibility(View.GONE);
				((TextView)findViewById(R.id.invite_not_members_text)).setVisibility(View.GONE);
			}
			
			if(entangleMembers.length() != 0){
				LinearLayout entangleMembersLayout = ((LinearLayout)findViewById(R.id.invite_entangle_members));
				for(int i=0;i<entangleMembers.length();i++){
					TextView newView = new TextView(this);
					newView.setText(entangleMembers.getString(i));
					entangleMembersLayout.addView(newView);
				}
			}else{
				((LinearLayout)findViewById(R.id.invite_entangle_members)).setVisibility(View.GONE);
				((TextView)findViewById(R.id.invite_entangle_members_text)).setVisibility(View.GONE);
			}
			
			if(alreadyInTheTangle.length() != 0){
				LinearLayout alreadyInTheTangleLayout = ((LinearLayout)findViewById(R.id.invite_already_in_tangle));
				for(int i=0;i<alreadyInTheTangle.length();i++){
					TextView newView = new TextView(this);
					newView.setText(alreadyInTheTangle.getString(i));
					alreadyInTheTangleLayout.addView(newView);
				}
			}else{
				((LinearLayout)findViewById(R.id.invite_already_in_tangle)).setVisibility(View.GONE);
				((TextView)findViewById(R.id.invite_already_in_tangle_text)).setVisibility(View.GONE);
			}
			
			if(invalid.length() != 0){
				LinearLayout invalidLayout = ((LinearLayout)findViewById(R.id.invite_invalid_emails));
				for(int i=0;i<invalid.length();i++){
					TextView newView = new TextView(this);
					newView.setText(invalid.getString(i));
					invalidLayout.addView(newView);
				}
			}else{
				((LinearLayout)findViewById(R.id.invite_invalid_emails)).setVisibility(View.GONE);
				((TextView)findViewById(R.id.invite_invalid_emails_text)).setVisibility(View.GONE);
			}
			
		} catch (JSONException e) {
			e.printStackTrace();
		}
		
	}
	
	/**
	 * The callback for the invite point , gets all the valid and not in the
	 * tangle emails and send them to the server to send an invitation to them
	 * @param view
	 */
	public void invite(View view){
		JSONArray finalEmails = new JSONArray();
		
		for(int i=0;i<notMembers.length();i++){
			try {
				finalEmails.put(notMembers.getString(i));
			} catch (JSONException e) {
				e.printStackTrace();
			}
		}
		
		for(int i=0;i<entangleMembers.length();i++){
			try {
				finalEmails.put(entangleMembers.getString(i));
			} catch (JSONException e) {
				e.printStackTrace();
			}
		}
		
		String message = ((EditText)findViewById(R.id.invite_invitation_message)).getText().toString();
		JSONObject json = new JSONObject();
		try {
			json.put("emails", finalEmails);
			json.put("message", message);
		} catch (JSONException e) {
			e.printStackTrace();
		}
		
		PostRequest postRequest = new PostRequest(Config.API_BASE_URL+"/tangle/" + tangleId +"/invite"){
			public void onPostExecute(String response){
				Toast.makeText(getApplicationContext(), "Invited !", Toast.LENGTH_LONG).show();
				// TODO
			}
		};
		
		postRequest.addHeader(Config.API_SESSION_ID, sessionId);
		postRequest.setBody(json);
		postRequest.execute();
	}
}
