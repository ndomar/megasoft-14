package com.megasoft.entangle;


import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.entangle.R;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PutRequest;

import android.app.Activity;
//import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.View;
//import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.Spinner;

public class EditProfileActivity extends Activity {
	SharedPreferences settings;
	String sessionId;
	// private CheckBox chk;
	String emails;
	String[] userEmails;
	EditText currentDescription = (EditText) findViewById(R.id.CurrentDescription);
	Spinner newday = (Spinner) findViewById(R.id.days);
	Spinner newmonth = (Spinner) findViewById(R.id.months);
	Spinner newyear = (Spinner) findViewById(R.id.years);
	EditText currentPassword = (EditText) findViewById(R.id.AddCurrentPassword);
	EditText newPassword = (EditText) findViewById(R.id.AddNewPassword);
	EditText confirmPassword = (EditText) findViewById(R.id.AddNewPasswordConfirmation);
	EditText addedMail = (EditText) findViewById(R.id.AddedMail);

	JSONObject whoamiResponse;

	// Layout layout = (R.layout.activity_edit_profile);
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		JSONObject whoamiRequest = new JSONObject();
		try {
			whoamiRequest.put("session Id", sessionId);

		} catch (JSONException e) {
			e.printStackTrace();
		}

		GetRequest getRequest = new GetRequest(Config.API_BASE_URL + "/user/"
				+ "/whoami") {
			public void onPostExecute(String response) {
				try {
					whoamiResponse = new JSONObject(response);
					// currentDescription.setText(whoamiResponse.getString("Description"));
					// currentDOB.setText(whoamiResponse.getString("Date_of_birth"));
					// emails = whoamiResponse.getString("Emails");
					// userEmails = emails.split(",");

				} catch (JSONException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				finish();
			}
		};
		/*
		 * for (int i = 0; i < userEmails.length; i++) { chk = new
		 * CheckBox(this); chk.setText(userEmails[i]); addContentView(chk,
		 * null);
		 * 
		 * }
		 */
		getRequest.addHeader(Config.API_SESSION_ID, sessionId);
		getRequest.execute();
		getIntent();
		setContentView(R.layout.activity_edit_profile);
	}

	public void saveAll(View view) {
		PutRequest putRequest = new PutRequest(Config.API_BASE_URL + "/user/"
				+ "edit");
		putRequest.addHeader(Config.API_SESSION_ID, sessionId);
		try {
			whoamiResponse.put("Description", currentDescription.getText()
					.toString());
			whoamiResponse.put("day", newday.getSelectedItem().toString());
			whoamiResponse.put("month", newmonth.getSelectedItem().toString());
			whoamiResponse.put("year", newyear.getSelectedItem().toString());
			whoamiResponse.put("current_password", currentPassword.getText()
					.toString());
			whoamiResponse
					.put("new_password", newPassword.getText().toString());
			whoamiResponse.put("confirm_password", confirmPassword.getText()
					.toString());
			whoamiResponse.put("added_mail", addedMail.getText().toString());

		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		putRequest.setBody(whoamiResponse);
		putRequest.execute();
		/*
		 * Intent viewEditedProfile = new Intent(this, MyProfile.class);
		 * startActivity(viewEditedProfile);
		 */

	}

}
