package com.megasoft.entangle;

import java.util.ArrayList;
import java.util.HashMap;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.text.InputType;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

public class InviteUserActivity extends Activity {
	/**
	 * The tangle Id that we want to invite users to
	 */
	int tangleId;

	/**
	 * The session Id of the currently logged in user
	 */
	String sessionId;

	/**
	 * The preferences instance
	 */
	SharedPreferences settings;

	/**
	 * The Layout that contain the emails edit texts
	 */
	LinearLayout layout;
	
	HashMap<Button, LinearLayout> removeLayoutButtons;
	
	HashMap<Button, EditText> editTexts;
	

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_invite_users);
		this.tangleId = getIntent().getIntExtra("tangleId", -1);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		this.layout = (LinearLayout) findViewById(R.id.invite_emails);
		this.editTexts = new HashMap<Button, EditText>();
		this.removeLayoutButtons = new HashMap<Button, LinearLayout>();
		this.addEmailField(null);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.invite_users, menu);
		return true;
	}

	/**
	 * The callback for adding a new email field
	 * 
	 * @param view
	 *            The add email button
	 * @author MohamedBassem
	 */
	public void addEmailField(View view) {

		EditText newEditText = new EditText(this);
		newEditText.setHint(R.string.user_email);
		newEditText.setInputType(InputType.TYPE_TEXT_VARIATION_EMAIL_ADDRESS);
		
		Button removeEmail = new Button(getApplicationContext());
		removeEmail.setText("R");
		
		LinearLayout emailLayout = new LinearLayout(getApplicationContext());
		emailLayout.setOrientation(LinearLayout.HORIZONTAL);
		emailLayout.addView(newEditText);
		emailLayout.addView(removeEmail);
		
		editTexts.put(removeEmail, newEditText);
		removeLayoutButtons.put(removeEmail, emailLayout);
		
		removeEmail.setOnClickListener(new View.OnClickListener() {
			
			@Override
			public void onClick(View v) {
				layout.removeView(removeLayoutButtons.get(v));
				removeLayoutButtons.remove(v);
				editTexts.remove(v);
				
			}
		});
		
		layout.addView(emailLayout, new LinearLayout.LayoutParams(
				LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT));
		
		newEditText.requestFocus();
		newEditText.setCursorVisible(true);
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
		for (Button removeButton : editTexts.keySet()) {
			EditText emailEditText = editTexts.get(removeButton);
			String val = emailEditText.getText().toString();
			if ( isValidEmail(val) ) {
				emailEditText.setError("Invalid Email");
				hasErrors = true;
				continue;
			} else {
				emails.put(val);
			}
		}
		
		if(hasErrors){
			Toast.makeText(this, "Please Fix Invalid Emails", Toast.LENGTH_SHORT).show();
			return;
		}

		JSONObject request = new JSONObject();
		try {
			request.put("emails", emails);
		} catch (JSONException e) {
			e.printStackTrace();
		}

		PostRequest postRequest = new PostRequest(Config.API_BASE_URL
				+ "/tangle/" + tangleId + "/invite") {
			public void onPostExecute(String response) {
				if(this.getStatusCode() == 200){ 
					onSuccess(response);
					view.setEnabled(true);
				}else{
					showErrorToast();
					view.setEnabled(true);
				}
			}
		};

		postRequest.addHeader(Config.API_SESSION_ID, sessionId);
		postRequest.setBody(request);
		postRequest.execute();
	}

	private boolean isValidEmail(String val) {
		String regex = "^[_a-z0-9-]+(\\.[_a-z0-9-]+)*@[a-z0-9-]+(\\.[a-z0-9-]+)*(\\.[a-z]{2,4})$";
		return val.matches(regex);
	}
	
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

}
