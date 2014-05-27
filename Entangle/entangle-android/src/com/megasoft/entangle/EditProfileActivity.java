package com.megasoft.entangle;

import java.io.ByteArrayOutputStream;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.util.Base64;
import android.view.View;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PutRequest;

@SuppressLint("DefaultLocale")
public class EditProfileActivity extends FragmentActivity implements
		AddEmailInterface {
	JSONArray currentEmails;
	final Calendar calendar = Calendar.getInstance();
	static final int DATE_DIALOG_ID = 0;
	SharedPreferences settings;
	String sessionId;
	private ArrayList<EmailEntryFragment> emails = new ArrayList<EmailEntryFragment>();
	String oldDescription;
	String oldDOB[];
	String[] day;
	String[] userEmails;
	CheckBox emailNotification;
	EditText currentDescription;
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
	private int userId;
	private static final String EMAIL_PATTERN = "^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@"
			+ "[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$";
	private static final String EDITPROFILE = "/user/edit";

	private static final String RETRIEVEDATA = "/user/retrieveData";

	JSONObject putReJsonObject = new JSONObject();
	Boolean notification;
	protected int newYear;
	protected int newMonth;
	protected int newDay;
	private int emailsCount;
	private boolean isDestroyed;
	private Button dataPicker;
	private static final int REQUEST_CODE = 1;
	String encodedImage;

	@SuppressLint("SimpleDateFormat")
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_edit_profile);
		getActionBar().hide();
		initializeView();
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		GetRequest getRequest = new GetRequest(Config.API_BASE_URL
				+ RETRIEVEDATA) {
			public void onPostExecute(String response) {
				if (isDestroyed) {
					return;
				}
				try {
					retrieveDataResponse = new JSONObject(response);
					try {
						userId = retrieveDataResponse.getInt("userId");
						oldDescription = retrieveDataResponse
								.getString("description");
						oldBirthDate = retrieveDataResponse
								.getJSONObject("date_of_birth");
						date = oldBirthDate.getString("date");
						splittedDate = date.split("-");
						day = splittedDate[2].split(" ");
						newYear = Integer.parseInt(splittedDate[0]);
						newMonth = Integer.parseInt(splittedDate[1]) - 1;
						newDay = Integer.parseInt(day[0]);
						dataPicker.setText(newDay + "/" + (newMonth + 1) + "/"
								+ newYear);
						currentDescription.setText(oldDescription);
						notification = retrieveDataResponse
								.getBoolean("notification_state");
						emailNotification.setChecked(notification);
						currentEmails = retrieveDataResponse
								.getJSONArray("emails");

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

	}

	/**
	 * This method adds an email field to type the mail into
	 * 
	 * @author maisaraFarahat
	 */
	public void addEmailField() {
		EmailEntryFragment newEmail = new EmailEntryFragment();
		newEmail.setActivity(this);
		emails.add(newEmail);
		emailsCount++;

		getSupportFragmentManager().beginTransaction()
				.add(R.id.user_emails, newEmail).commit();

	}

	@SuppressWarnings("deprecation")
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
		currentDescription = (EditText) findViewById(R.id.CurrentDescription);
		dataPicker = (Button) findViewById(R.id.DatePickerButton);
	}

	private DatePickerDialog.OnDateSetListener mDateSetListener = new DatePickerDialog.OnDateSetListener() {
		public void onDateSet(DatePicker view, int year, int monthOfYear,
				int dayOfMonth) {
			newYear = year;
			newMonth = monthOfYear + 1;
			newDay = dayOfMonth;
			dataPicker.setText(newDay + "/" + newMonth + "/" + newYear);
		}
	};

	@Override
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
	public void saveAll(View view) {
		if ((oldDescription.equals(currentDescription.getText().toString()))
				&& (day[0].equals(String.valueOf(newDay)))
				&& (splittedDate[1].equals(String.valueOf(newMonth)))
				&& (splittedDate[0].equals(String.valueOf(newYear)))
				&& emails.isEmpty() && (!emailNotification.isChecked())) {
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

				String date = newYear + "-" + newMonth + "-" + newDay
						+ " 00:00:00";

				putReJsonObject.put("description", currentDescription.getText()
						.toString());
				putReJsonObject.put("new_date_of_birth", date);
				boolean hasErrors = false;

				view.setEnabled(false);
				// TODO Edit here to support multiple emails
				JSONArray emails = currentEmails;

				if (hasErrors) {
					view.setEnabled(true);
					return;
				}
				putReJsonObject.put("emails", emails);
				putReJsonObject.put("icon", encodedImage);
			} catch (JSONException e) {
				e.printStackTrace();
			}

			PutRequest putRequest = new PutRequest(Config.API_BASE_URL
					+ EDITPROFILE) {
				protected void onPostExecute(String result) {
					if (isDestroyed) {
						return;
					}
					if (this.getStatusCode() == 200) {
						goToGeneralProfileActivity();

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
	private void goToGeneralProfileActivity() {
		this.finish();
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

	/**
	 * This method removes the fragment when deleting an email
	 * 
	 * @param EmailEntryFragment
	 * 
	 * @author maisaraFarahat
	 */
	public void removeEmailField(EmailEntryFragment emailEntryFragment) {
		if (emailsCount == 1) {
			emailEntryFragment.getEditText().setText("");
		} else {
			emails.remove(emailEntryFragment);
			emailsCount--;
		}

	}

	/**
	 * This methods cancels the edit action
	 * 
	 * @param View
	 * 
	 * 
	 * @author maisaraFarahat
	 */
	public void cancelRedirect(View view) {
		this.finish();
	}

	public void onPause() {
		super.onPause();
		isDestroyed = true;
	}

	public void goToGallery(View view) {
		 startActivityForResult(new Intent(Intent.ACTION_PICK,
		 android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI),
		 REQUEST_CODE);
//		Intent pickIntent = new Intent();
//		pickIntent.setType("image/*");
//		pickIntent.setAction(Intent.ACTION_PICK);
//
//		Intent takePhotoIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
//		startActivityForResult(takePhotoIntent, REQUEST_CODE);
//		takePhotoIntent.setFlags(1);

//		String pickTitle = "Select or take a new Picture"; // Or get from
//															// strings.xml
//		Intent chooserIntent = Intent.createChooser(pickIntent, pickTitle);
//		chooserIntent.putExtra(Intent.EXTRA_INITIAL_INTENTS,
//				new Intent[] { takePhotoIntent });
//
//		startActivityForResult(chooserIntent, REQUEST_CODE);
	}

	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		if (resultCode == RESULT_OK && requestCode == REQUEST_CODE
				&& null != data) {
			Bitmap bitmap = getPhotoPath(data.getData());
			ByteArrayOutputStream baos = new ByteArrayOutputStream();
			bitmap.compress(Bitmap.CompressFormat.PNG, 100, baos);
			byte[] byteArray = baos.toByteArray();
			encodedImage = Base64.encodeToString(byteArray, Base64.DEFAULT);
		}
//		 else {
//				Bitmap bitmap = (Bitmap) data.getExtras().get("data");
//				ByteArrayOutputStream baos = new ByteArrayOutputStream();
//				bitmap.compress(Bitmap.CompressFormat.PNG, 100, baos);
//				byte[] byteArray = baos.toByteArray();
//				encodedImage = Base64.encodeToString(byteArray, Base64.DEFAULT);
//		}
	}

	public Bitmap getPhotoPath(Uri uri) {
		String[] projection = { android.provider.MediaStore.Images.Media.DATA };
		Cursor cursor = getContentResolver().query(uri, projection, null, null,
				null);
		int columnIndex = cursor.getColumnIndexOrThrow(projection[0]);
		cursor.moveToFirst();
		String filePath = cursor.getString(columnIndex);
		cursor.close();
		Bitmap bitmap = BitmapFactory.decodeFile(filePath);
		return bitmap;
	}

}
