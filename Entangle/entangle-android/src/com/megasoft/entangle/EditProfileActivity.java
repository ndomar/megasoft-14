package com.megasoft.entangle;

import java.text.SimpleDateFormat;
import org.json.JSONException;
import org.json.JSONObject;
import com.megasoft.config.Config;
import com.megasoft.entangle.R;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PutRequest;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;
import android.widget.Spinner;

public class EditProfileActivity extends Activity {
	SharedPreferences settings;
	String sessionId;
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
	String oldBirthDate;
	String[] splittedDate;
	JSONObject whoamiResponse;

	@SuppressLint("SimpleDateFormat")
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_edit_profile);
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
				} catch (JSONException e) {
					e.printStackTrace();
				}
				finish();
			}
		};

		getRequest.addHeader(Config.API_SESSION_ID, sessionId);
		getRequest.execute();
		try {
			SimpleDateFormat dateformatJava = new SimpleDateFormat("dd-MM-yyyy");
			oldBirthDate = dateformatJava.format(whoamiResponse
					.get("date_of_birth"));
			splittedDate = oldBirthDate.split("-");
			newday.setSelection(Integer.parseInt(splittedDate[0]) - 1);
			newmonth.setSelection(Integer.parseInt(splittedDate[1]) - 1);
			newyear.setSelection(Integer.parseInt(splittedDate[2]) - 1951);
			currentDescription.setText(whoamiResponse.getString("Description"));

		} catch (JSONException e) {
			e.printStackTrace();
		}
		
		getIntent();
		setContentView(R.layout.activity_edit_profile);
	}

	JSONObject putReJsonObject = new JSONObject();

	public void saveAll(View view) {
		PutRequest putRequest = new PutRequest(Config.API_BASE_URL + "/user/"
				+ "edit");
		putRequest.addHeader(Config.API_SESSION_ID, sessionId);
		try {
			String date = newday.getSelectedItem().toString() + "-"
					+ newmonth.getSelectedItem().toString() + "-"
					+ newyear.getSelectedItem().toString();
			putReJsonObject.put("newDescription", currentDescription.getText()
					.toString());
			putReJsonObject.put("new_date_of_birth", date);
			putReJsonObject.put("current_password", currentPassword.getText()
					.toString());
			putReJsonObject.put("new_password", newPassword.getText()
					.toString());
			putReJsonObject.put("confirm_password", confirmPassword.getText()
					.toString());
			putReJsonObject.put("added_mail", addedMail.getText().toString());

		} catch (JSONException e) {
			e.printStackTrace();
		}
		putRequest.addHeader(Config.API_SESSION_ID, sessionId);
		putRequest.setBody(putReJsonObject);
		putRequest.execute();

		Intent viewEditedProfile = new Intent(this, MyProfile.class);
		startActivity(viewEditedProfile);

	}

}
