package com.megasoft.entangle;

import java.util.ArrayList;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.text.InputType;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;

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

	/**
	 * Arraylist of all edit texts in the layout
	 */
	ArrayList<EditText> editTexts;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_invite_users);

		this.tangleId = getIntent().getIntExtra(
				"com.megasoft.entangle.tangleId", -1);

		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");

		this.layout = (LinearLayout) findViewById(R.id.invite_emails);

		this.editTexts = new ArrayList<EditText>();

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
		editTexts.add(newEditText);
		layout.addView(newEditText, new LinearLayout.LayoutParams(
				LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT));
	}

	/**
	 * The callback for the continue button. It gets all the emails and put them
	 * in a JSON and send them to an endpoint to validate and classify them
	 * 
	 * @param view
	 *            The go to confirmation button
	 * @author MohamedBassem
	 */
	public void goToConfirmationActivity(View view) {
		JSONArray emails = new JSONArray();
		for (EditText emailEditText : editTexts) {
			String val = emailEditText.getText().toString();
			if (val.equals("")) {
				continue;
			} else {
				emails.put(val);
			}
		}

		JSONObject request = new JSONObject();
		try {
			request.put("emails", emails);
		} catch (JSONException e) {
			e.printStackTrace();
		}

		PostRequest postRequest = new PostRequest(Config.API_BASE_URL
				+ "/tangle/" + tangleId + "/check-membership") {
			public void onPostExecute(String response) {
				goToConfirmation(response);
			}
		};

		postRequest.addHeader(Config.API_SESSION_ID, sessionId);
		postRequest.setBody(request);
		postRequest.execute();
	}

	/**
	 * The callback of the request , opens the new activity and passes the JSON
	 * response to it
	 * 
	 * @param response
	 *            The JSON response from the previous activity
	 * @author MohamedBassem
	 */
	public void goToConfirmation(String response) {
		Intent inviteUserConfirmation = new Intent(this,
				ConfirmInviteUserActivity.class);
		inviteUserConfirmation.putExtra("com.megasoft.entangle.emails",
				response);
		inviteUserConfirmation.putExtra("com.megasoft.entangle.tangleId",
				tangleId);
		startActivity(inviteUserConfirmation);
	}

}
