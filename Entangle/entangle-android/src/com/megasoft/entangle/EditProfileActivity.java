package com.megasoft.entangle;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.entangle.R;
import com.megasoft.entangle.EmailEntryFragment;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PutRequest;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.Fragment;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Log;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.Toast;

@SuppressLint("DefaultLocale")
public class EditProfileActivity extends Activity {
	final Calendar calendar = Calendar.getInstance();
	static final int DATE_DIALOG_ID = 0;
	SharedPreferences settings;
	String sessionId;
	private ArrayList<EmailEntryFragment> emails;
	String oldDescription;
	String oldDOB[];
	String[] day;
	String[] userEmails;
	CheckBox emailNotification;
	EditText currentDescription;
	// EditText currentPassword;
	// EditText newPassword;
	// EditText confirmPassword;
	EditText addedMail;
	JSONObject oldBirthDate;
	String date;
	String[] splittedDate;
	JSONObject retrieveDataResponse;
	String addedEmail;
	Activity currentActivity = this;
	Intent viewEditedProfile;
	private Pattern pattern;
	private Matcher matcher;

	private static final String EMAIL_PATTERN = "^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@"
			+ "[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$";
	private static final String EDITPROFILE = "/user/edit";
	private static final String RETRIEVEDATA = "/user/retrieveData";
	JSONObject putReJsonObject = new JSONObject();
	Boolean notification = true;
	protected int newYear;
	protected int newMonth;
	protected int newDay;
	private int emailsCount;

	@SuppressLint("SimpleDateFormat")
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_edit_profile);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		GetRequest getRequest = new GetRequest(Config.API_BASE_URL_SERVER
				+ RETRIEVEDATA) {
			public void onPostExecute(String response) {
				try {
					retrieveDataResponse = new JSONObject(response);
					try {
						oldDescription = retrieveDataResponse
								.getString("description");
						oldBirthDate = retrieveDataResponse
								.getJSONObject("date_of_birth");
						date = oldBirthDate.getString("date");
						splittedDate = date.split("-");
						Log.i("Message", date);
						day = splittedDate[2].split(" ");
						newYear = Integer.parseInt(splittedDate[0]);
						newMonth = Integer.parseInt(splittedDate[1]) - 1;
						newDay = Integer.parseInt(day[0]);
						currentDescription.setText(oldDescription);
						notification = retrieveDataResponse
								.getBoolean("notification_state");
					} catch (JSONException e) {
						e.printStackTrace();
					}

				} catch (JSONException e) {
					e.printStackTrace();
				}
			}
		};

		getRequest.addHeader(Config.API_SESSION_ID, sessionId);
		getRequest.execute();
		initializeView();
	}

	
	public void addEmailField() {
		EmailEntryFragment newEmail = new EmailEntryFragment();
	//	newEmail.setActivity(this);
		emails.add(newEmail);
		emailsCount ++;
	}
	public void selectDOB(View view) {
		showDialog(DATE_DIALOG_ID);

	}

	/**
	 * This Method Initilaze the view
	 * 
	 * @author menna
	 */
	private void initializeView() {
		emailNotification = (CheckBox) findViewById(R.id.set_notification);
		if (!notification) {
			emailNotification.setText("Turn on notification");
		}
		currentDescription = (EditText) findViewById(R.id.CurrentDescription);
		addedMail = (EditText) findViewById(R.id.AddedMail);
	}

	private DatePickerDialog.OnDateSetListener mDateSetListener = new DatePickerDialog.OnDateSetListener() {
		public void onDateSet(DatePicker view, int year, int monthOfYear,
				int dayOfMonth) {
			newYear = year;
			newMonth = monthOfYear;
			newDay = dayOfMonth;
		}
	};

	protected Dialog onCreateDialog(int id) {
		switch (id) {
		case DATE_DIALOG_ID:
			return new DatePickerDialog(this, mDateSetListener, newYear,
					newMonth, newDay);
		}
		return null;

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
		if ((oldDescription.equals(currentDescription.getText().toString()))
				// && (day[0].equals(newday.getSelectedItem()))
				// && (splittedDate[1].equals(newmonth.getSelectedItem()))
				// && (splittedDate[0].equals(newyear.getSelectedItem()))
				// && (currentPassword.getText().toString().matches(""))
				// && (newPassword.getText().toString().matches(""))
				// && (currentPassword.getText().toString().matches(""))
				&& (!emailNotification.isChecked())
				&& (addedMail).getText().toString().matches("")) {
			Context context = getApplicationContext();
			CharSequence text = "Nothing has been changed";
			int duration = Toast.LENGTH_SHORT;
			Toast toast = Toast.makeText(context, text, duration);
			toast.show();
		} else {
			try {
				if (emailNotification.isChecked()) {
					putReJsonObject.put("notification_state", true);
				} else {
					putReJsonObject.put("notification_state", false);
				}

				 String date = newYear + "-"
				 + newMonth
				 + "-" + newDay+" 00:00:00";

				putReJsonObject.put("description", currentDescription.getText()
						.toString());
				 putReJsonObject.put("new_date_of_birth", date);
				
				addedEmail = addedMail.getText().toString();
				if ((!(emailValidator(addedEmail)))
						&& (!(addedEmail.matches("")))) {
					addedMail.setError("This is not a valid Email");
					return;
				}
				
				putReJsonObject.put("added_email", addedMail.getText()
						.toString());
			} catch (JSONException e) {
				e.printStackTrace();
			}

			PutRequest putRequest = new PutRequest(Config.API_BASE_URL_SERVER
					+ EDITPROFILE) {
				protected void onPostExecute(String result) {
					Log.i("Message",
							this.getErrorMessage() + this.getStatusCode()
									+ this.getStatus());
					if (this.getStatusCode() == 200) {
						getActivity();
						finish();
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
			putRequest.setBody(putReJsonObject);
			putRequest.execute();
		}
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
	 * This emails check that the String is in Email format
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


	public void removeEmailField(EmailEntryFragment emailEntryFragment) {
		// TODO Auto-generated method stub
		
	}

}
