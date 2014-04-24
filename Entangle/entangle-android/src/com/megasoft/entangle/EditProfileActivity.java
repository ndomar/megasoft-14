package com.megasoft.entangle;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.entangle.R;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PutRequest;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Log;
import android.view.View;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.Toast;

@SuppressLint("DefaultLocale")
public class EditProfileActivity extends Activity {
	SharedPreferences settings;
	String sessionId;
	String emails;
	String[] userEmails;
	CheckBox emailNotification;
	EditText currentDescription;
	Spinner newday;
	Spinner newmonth;
	Spinner newyear;
	EditText currentPassword;
	EditText newPassword;
	EditText confirmPassword;
	EditText addedMail;
	String oldBirthDate;
	String[] splittedDate;
	JSONObject retrieveDataResponse;
	String addedEmail;
	Activity currentActivity = this;
	Intent viewEditedProfile;
	private Pattern pattern;
	private Matcher matcher;
	private static final String EMAIL_PATTERN = "^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@"
			+ "[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$";
	JSONObject putReJsonObject = new JSONObject();
	Boolean notification = true;

	@SuppressLint("SimpleDateFormat")
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_edit_profile);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");

		GetRequest getRequest = new GetRequest(Config.API_BASE_URL + "/user/"
				+ "/retrieveData") {
			public void onPostExecute(String response) {
				try {
					retrieveDataResponse = new JSONObject(response);
				} catch (JSONException e) {
					e.printStackTrace();
				}
				finish();
			}
		};

		getRequest.addHeader(Config.API_SESSION_ID, sessionId);
		getRequest.execute();
		try {

			oldBirthDate = retrieveDataResponse.getString("date_of_birth");
			splittedDate = oldBirthDate.split("-");
			newday.setSelection(Integer.parseInt(splittedDate[0]) - 1);
			newmonth.setSelection(Integer.parseInt(splittedDate[1]) - 1);
			newyear.setSelection(Integer.parseInt(splittedDate[2]) - 1951);
			currentDescription.setText(retrieveDataResponse
					.getString("description"));
			notification = retrieveDataResponse
					.getBoolean("notification_state");
		} catch (JSONException e) {
			e.printStackTrace();
		}

		initializeView();
	}

	/**
	 * This Method Initilaze the view
	 * 
	 * @author menna
	 */
	private void initializeView() {
		emailNotification = (CheckBox) findViewById(R.id.set_notification);
		if (notification != true) {
			emailNotification.setText("Turn on notification");
		}

		currentDescription = (EditText) findViewById(R.id.CurrentDescription);
		newday = (Spinner) findViewById(R.id.days);
		newmonth = (Spinner) findViewById(R.id.months);
		newyear = (Spinner) findViewById(R.id.years);
		currentPassword = (EditText) findViewById(R.id.AddCurrentPassword);
		newPassword = (EditText) findViewById(R.id.AddNewPassword);
		confirmPassword = (EditText) findViewById(R.id.AddNewPasswordConfirmation);
		addedMail = (EditText) findViewById(R.id.AddedMail);
	}

	/**
	 * This method runs on button save click and saves all edited data
	 * 
	 * @param View
	 *            view
	 * @author menna
	 */
	@SuppressLint("SimpleDateFormat")
	public void saveAll(View view) {

		PutRequest putRequest = new PutRequest(Config.API_BASE_URL + "/user/"
				+ "edit") {
			protected void onPostExecute(String result) {
				if (this.getStatusCode() == 200) {
					getActivity();
					startActivity(viewEditedProfile);
				} else {
					Context context = getApplicationContext();
					CharSequence text = "An Internal Error please try again";
					int duration = Toast.LENGTH_SHORT;
					Toast toast = Toast.makeText(context, text, duration);
					toast.show();
				}
			}
		};
		putRequest.addHeader(Config.API_SESSION_ID, sessionId);
		try {

			String notificationState = emailNotification.getText().toString();
			if (notificationState.equals("Turn off notification")) {
				if ((notification == true && emailNotification.isChecked())
						|| (notification == false && !emailNotification
								.isChecked())) {
					putReJsonObject.put("notification_state", false);
				} else {
					putReJsonObject.put("notification_state", true);
				}
			}
			String date = newday.getSelectedItem().toString() + "-"
					+ getMonthNumber(newmonth.getSelectedItem().toString())
					+ "-" + newyear.getSelectedItem().toString();

			putReJsonObject.put("description", currentDescription.getText()
					.toString());
			putReJsonObject.put("new_date_of_birth", date);
			if (!(TextUtils.isEmpty(currentPassword.getText().toString())
					&& TextUtils.isEmpty(newPassword.getText().toString()) && TextUtils
						.isEmpty(confirmPassword.getText().toString()))) {
				if (newPassword.getText().toString().length() < 8) {
					newPassword
							.setError("Your Password must contain at least 8 characters");
					return;
				}
				if (!confirmPassword.getText().toString()
						.equals(newPassword.getText().toString())) {
					newPassword.setError("Your Password doesn't match");
				}
			}
			addedEmail = addedMail.getText().toString();

			if (!(emailValidator(addedEmail))) {
				addedMail.setError("This is not a valid Email");
				return;
			}

			putReJsonObject.put("current_password", currentPassword.getText()
					.toString());
			putReJsonObject.put("new_password", newPassword.getText()
					.toString());
			putReJsonObject.put("confirm_password", confirmPassword.getText()
					.toString());

			putReJsonObject.put("email", addedMail.getText().toString());
			Log.i("batata", "anageeet");
		} catch (JSONException e) {
			e.printStackTrace();
		}
		putRequest.addHeader(Config.API_SESSION_ID, sessionId);
		putRequest.setBody(putReJsonObject);
		putRequest.execute();

	}

	/**
	 * This Method sets the Intent using the current activity
	 * 
	 * @author menna
	 */
	private void getActivity() {
		viewEditedProfile = new Intent(this, ProfileActivity.class);
	}

	/**
	 * This emails chech that the String is in Email format
	 * 
	 * @param String
	 *            email
	 * @return boolean
	 * @author menna
	 */
	public boolean emailValidator(String email) {
		pattern = Pattern.compile(EMAIL_PATTERN);
		matcher = pattern.matcher(email);
		return matcher.matches();
	}

	/**
	 * This method converts month name into number
	 * 
	 * @param String
	 *            month
	 * @return String monthNumber
	 * @author menna
	 */
	public static String getMonthNumber(String month) {

		String monthNumber = "00";

		if (month == null) {
			return monthNumber;
		}

		switch (month.toLowerCase()) {
		case "january":
			monthNumber = "01";
			break;
		case "february":
			monthNumber = "02";
			break;
		case "march":
			monthNumber = "03";
			break;
		case "april":
			monthNumber = "04";
			break;
		case "may":
			monthNumber = "05";
			break;
		case "june":
			monthNumber = "06";
			break;
		case "july":
			monthNumber = "07";
			break;
		case "august":
			monthNumber = "08";
			break;
		case "september":
			monthNumber = "09";
			break;
		case "october":
			monthNumber = "10";
			break;
		case "november":
			monthNumber = "11";
			break;
		case "december":
			monthNumber = "12";
			break;
		default:
			monthNumber = "00";
			break;
		}

		return monthNumber;
	}

}
