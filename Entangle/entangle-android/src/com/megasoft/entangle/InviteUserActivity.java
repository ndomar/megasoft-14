package com.megasoft.entangle;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Context;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

public class InviteUserActivity extends FragmentActivity {
	/**
	 * The tangle Id that we want to invite users to
	 */
	private int tangleId;

	/**
	 * The session Id of the currently logged in user
	 */
	private String sessionId;

	/**
	 * The preferences instance
	 */
	private SharedPreferences settings;
	
	/**
	 * An arraylist of the email fields in the activity
	 */
	private ArrayList<EmailEntryFragment> emails;
	
	/**
	 * The number of fields in the activity
	 */
	private int emailsCount = 0;
	
	/**
	 * The layout that contains the email fields
	 */
	LinearLayout layout;
	
	

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_invite_users);
		getActionBar().hide();
		this.tangleId = getIntent().getIntExtra("tangleId", 1);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		this.layout = (LinearLayout) findViewById(R.id.invite_emails);
		this.emails = new ArrayList<EmailEntryFragment>();
		this.addEmailField();
	}

	/**
	 * The callback for adding a new email field
	 * 
	 * @param view
	 *            The add email button
	 * @author MohamedBassem
	 */
	public void addEmailField() {
		EmailEntryFragment newEmail = new EmailEntryFragment();
		newEmail.setActivity(this);
		emails.add(newEmail);
		emailsCount ++;
		getSupportFragmentManager().beginTransaction().add(R.id.invite_emails, newEmail).commit();
	}
	
	
	/**
	 * Removing a field when the remove button is pressed.
	 * @param emailEntryFragment
	 * @author MohamedBassem
	 */
	public void removeEmailField(EmailEntryFragment emailEntryFragment) {
		if(emailsCount == 1){
			emailEntryFragment.getEditText().setText("");
		}else{
			if(emails.indexOf(emailEntryFragment) == emails.size()-1){
				emails.get(emails.size()-2).setTextChangeListener();
			}
			getSupportFragmentManager().beginTransaction().remove(emailEntryFragment).commit();
			emails.remove(emailEntryFragment);
			emailsCount--;
		}
		
	}
	
	/**
	 * The callback for the continue button. It gets all the emails and put them
	 * in a JSON and send them to an endpoint to validate and classify them
	 * 
	 * @param view
	 *            The go to confirmation button
	 * @author MohamedBassem
	 */
	public void invite(final View view) {
		
		if(!isNetworkAvailable()){
			showErrorToast();
			return;
		}
		
		boolean hasErrors = false;
		
		view.setEnabled(false);
		
		JSONArray emails = new JSONArray();
		for (EmailEntryFragment email : this.emails) {
			String val = email.getEmail();
			if(val.equals("")){
				continue;
			}
			if ( !val.equals("") && !isValidEmail(val) ) {
				email.getEditText().setError("Invalid Email");
				hasErrors = true;
			} else {
				email.getEditText().setError(null);
				emails.put(val);
			}
		}
		
		if(hasErrors){
			view.setEnabled(true);
			return;
		}
		
		String invitationMessage = ((EditText)findViewById(R.id.invite_message)).getText().toString();
		JSONObject request = new JSONObject();
		try {
			request.put("emails", emails);
			request.put("message", invitationMessage);
		} catch (JSONException e) {
			e.printStackTrace();
		}
		Log.e("test", request.toString());
		PostRequest postRequest = new PostRequest(Config.API_BASE_URL
				+ "/tangle/" + tangleId + "/invite") {
			public void onPostExecute(String response) {
				if(this.getStatusCode() == 201){ 
					onSuccess(response);
					view.setEnabled(true);
				}else{
					Log.e("test",this.getErrorMessage());
					showErrorToast();
					view.setEnabled(true);
				}
			}
		};

		postRequest.addHeader(Config.API_SESSION_ID, sessionId);
		postRequest.setBody(request);
		postRequest.execute();
	}
	
	
	/**
	 * Validates that a certain email is in a correct format
	 * @param the email to be validated
	 * @return true if the email is in a valid format.
	 */
	private boolean isValidEmail(String email) {
		String regex = "^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@[a-z0-9-]+(\\.[a-z0-9-]+)*(\\.[a-z]{2,4})$";
		return email.matches(regex);
	}
	
	/**
	 * The success callback of the invitation process , which closes this activity
	 * @param response
	 */
	public void onSuccess(String response){
	
		try {
			JSONObject jsonReponse = new JSONObject(response);
			if(jsonReponse.getInt("pending") == 0){
				Toast.makeText(getApplicationContext(), "Invited !",
						Toast.LENGTH_LONG).show();
			}else{
				Toast.makeText(getApplicationContext(), "Waiting For Tangle Owner Approval !",
						Toast.LENGTH_LONG).show();
			}
			finish();
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}
	
	/**
	 * Checks the Internet connectivity.
	 * @return true if there is an Internet connection , false otherwise
	 */
	private boolean isNetworkAvailable() {
	    ConnectivityManager connectivityManager 
	          = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
	    NetworkInfo activeNetworkInfo = connectivityManager.getActiveNetworkInfo();
	    return activeNetworkInfo != null && activeNetworkInfo.isConnected();
	}
	
	/**
	 * Shows a something went wrong toast
	 */
	private void showErrorToast(){
		Toast.makeText(getApplicationContext(), "Sorry , Something went wrong.", Toast.LENGTH_SHORT).show();
	}
	
	/**
	 * Closes the current activity
	 * @param view
	 */
	public void closeActivity(View view){
		finish();
	}
}
