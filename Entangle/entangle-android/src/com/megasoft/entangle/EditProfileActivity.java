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
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.Toast;

@SuppressLint("DefaultLocale")
public class EditProfileActivity extends Activity {
	SharedPreferences settings;
	String sessionId;
	String emails;
	String[] userEmails;
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
	JSONObject whoamiResponse;
	String addedEmail;
	Activity currentActivity = this;
	Intent viewEditedProfile;
	private Pattern pattern;
	private Matcher matcher;
	private static final String EMAIL_PATTERN = "^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@"
			+ "[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$";
	JSONObject putReJsonObject = new JSONObject();

	@SuppressLint("SimpleDateFormat")
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_edit_profile);

		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		/*
		 * GetRequest getRequest = new GetRequest("http://menna.apiary-mock.com"
		 * + "/user/" + "/whoami/fdghdhjdgf") { public void onPostExecute(String
		 * response) { try { whoamiResponse = new JSONObject(response); } catch
		 * (JSONException e) { e.printStackTrace(); } finish(); } };
		 */

		// getRequest.addHeader(Config.API_SESSION_ID, sessionId);
		// getRequest.execute();
		// try {
		// SimpleDateFormat dateformatJava = new SimpleDateFormat("dd-MM-yyyy");
		// oldBirthDate = dateformatJava.format(whoamiResponse
		// .get("date_of_birth"));
		// splittedDate = oldBirthDate.split("-");
		// newday.setSelection(Integer.parseInt(splittedDate[0]) - 1);
		// newmonth.setSelection(Integer.parseInt(splittedDate[1]) - 1);
		// newyear.setSelection(Integer.parseInt(splittedDate[2]) - 1951);
		// currentDescription.setText(whoamiResponse.getString("description"));
		//
		// } catch (JSONException e) {
		// e.printStackTrace();
		// }

		initializeView();
	}

	private void initializeView() {
		currentDescription = (EditText) findViewById(R.id.CurrentDescription);
		newday = (Spinner) findViewById(R.id.days);
		newmonth = (Spinner) findViewById(R.id.months);
		newyear = (Spinner) findViewById(R.id.years);
		currentPassword = (EditText) findViewById(R.id.AddCurrentPassword);
		newPassword = (EditText) findViewById(R.id.AddNewPassword);
		confirmPassword = (EditText) findViewById(R.id.AddNewPasswordConfirmation);
		addedMail = (EditText) findViewById(R.id.AddedMail);
	}

	@SuppressLint("SimpleDateFormat")
	public void saveAll(View view) {
		PutRequest putRequest = new PutRequest(Config.API_BASE_URL + "/user/"
				+ "edit") {
			protected void onPostExecute(String result) {
				if (result.toString().equals("200 OK")) {
					getActivity();
					startActivity(viewEditedProfile);
				} else {
					Context context = getApplicationContext();
					CharSequence text = "Internal Error please try again";
					int duration = Toast.LENGTH_SHORT;
					Toast toast = Toast.makeText(context, text, duration);
					toast.show();
				}
			}
		};
		putRequest.addHeader(Config.API_SESSION_ID, sessionId);
		try {
			String date = newday.getSelectedItem().toString() + "-"
					+ getMonthNumber(newmonth.getSelectedItem().toString())
					+ "-" + newyear.getSelectedItem().toString();

			putReJsonObject.put("newDescription", currentDescription.getText()
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

			putReJsonObject.put("added_mail", addedMail.getText().toString());

		} catch (JSONException e) {
			e.printStackTrace();
		}
		putRequest.addHeader(Config.API_SESSION_ID, sessionId);
		putRequest.setBody(putReJsonObject);
		putRequest.execute();

	}

	private void getActivity() {
		viewEditedProfile = new Intent(this, EditProfileActivity.class);
	}

	public boolean emailValidator(String email) {
		pattern = Pattern.compile(EMAIL_PATTERN);
		matcher = pattern.matcher(email);
		return matcher.matches();
	}

	public static int getMonthNumber(String month) {

		int monthNumber = 0;

		if (month == null) {
			return monthNumber;
		}

		switch (month.toLowerCase()) {
		case "january":
			monthNumber = 1;
			break;
		case "february":
			monthNumber = 2;
			break;
		case "march":
			monthNumber = 3;
			break;
		case "april":
			monthNumber = 4;
			break;
		case "may":
			monthNumber = 5;
			break;
		case "june":
			monthNumber = 6;
			break;
		case "july":
			monthNumber = 7;
			break;
		case "august":
			monthNumber = 8;
			break;
		case "september":
			monthNumber = 9;
			break;
		case "october":
			monthNumber = 10;
			break;
		case "november":
			monthNumber = 11;
			break;
		case "december":
			monthNumber = 12;
			break;
		default:
			monthNumber = 0;
			break;
		}

		return monthNumber;
	}
}