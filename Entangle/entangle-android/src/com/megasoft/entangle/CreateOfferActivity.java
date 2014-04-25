package com.megasoft.entangle;

import java.util.Calendar;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.os.Bundle;
import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.content.Intent;
import android.content.SharedPreferences;
import android.view.Menu;
import android.view.View;
import android.view.View.OnFocusChangeListener;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

public class CreateOfferActivity extends Activity {
	/**
	 * preference instance
	 */
	SharedPreferences settings;
	/**
	 * sessionId of the currently logged in user
	 */
	String sessionId;
	/**
	 * EditText that will contain the offer's description
	 */
	EditText description;
	/**
	 * EditText that will contain the offer's requested price
	 */
	EditText requestedPrice;
	/**
	 * the Post Button that will send the server the postrequest to Post the
	 * offer
	 */
	Button Post;
	/**
	 * the json object to be sent in the postrequest
	 */
	JSONObject json = new JSONObject();
	/**
	 * the chosen deadLine year , in case user didn't choose a value it will be
	 * set to current year
	 */
	int deadLineYear;
	/**
	 * the chosen deadLine month , in case user didn't choose a value it will be
	 * set to current month
	 */
	int deadLineMonth;
	/**
	 * the chosen deadLine day , in case user didn't choose a value it will be
	 * set to current day
	 */
	int deadLineDay;
	/**
	 * Textview to display the deadLine date , in case user didn't choose a
	 * value it will display today's date
	 */
	TextView dateDisplay;
	/**
	 * Button clicked to pick deadLine date
	 */
	Button pickDate;
	/**
	 * CheckBox that the user should check after filling the fields
	 */
	CheckBox checkBox;
	/**
	 * the date dialog Id
	 */
	static final int DATE_DIALOG_ID = 0;
	/**
	 * calendar to choose deadLine date from
	 */
	final Calendar calendar = Calendar.getInstance();
	/**
	 * current day
	 */
	final int currentDay = calendar.get(Calendar.DAY_OF_MONTH);
	/**
	 * current month
	 */
	final int currentMonth = calendar.get(Calendar.MONTH);
	/**
	 * current year
	 */
	final int currentYear = calendar.get(Calendar.YEAR);
	/**
	 * String of the current date
	 */
	final String date = currentDay + "/" + (currentMonth + 1) + "/"
			+ currentYear;

	/**
	 * on creation of the activity it takes data from the fields and send it as
	 * json object on clicking the Post Button
	 * 
	 * @param savedInstanceState
	 * @return none
	 * @author Salma Khaled
	 */
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		Intent previousIntent = getIntent();
		final int tangleID = previousIntent.getIntExtra("tangleID", 0);
		final int requestID = previousIntent.getIntExtra("requestID", 0);
		settings = getSharedPreferences(Config.SETTING, 0);
		sessionId = settings.getString(Config.SESSION_ID, "");
		setContentView(R.layout.activity_create_offer);
		checkBox = (CheckBox) findViewById(R.id.checkBox);
		description = (EditText) findViewById(R.id.description);
		requestedPrice = (EditText) findViewById(R.id.requestedPrice);
		description.setOnFocusChangeListener(focusListener);
		requestedPrice.setOnFocusChangeListener(focusListener);
		Post = (Button) findViewById(R.id.Post);
		Post.setEnabled(false);
		dateDisplay = (TextView) findViewById(R.id.showMyDate);
		pickDate = (Button) findViewById(R.id.deadline);
		deadLineYear = calendar.get(Calendar.YEAR);
		deadLineMonth = calendar.get(Calendar.MONTH);
		deadLineDay = calendar.get(Calendar.DAY_OF_MONTH);
		final String currentDateTime = date + " " + calendar.get(Calendar.HOUR)
				+ ":" + calendar.get(Calendar.MINUTE) + ":"
				+ calendar.get(Calendar.SECOND);

		Post.setOnClickListener(new View.OnClickListener() {

			public void onClick(View arg0) {

				try {
					json.put("description", description.getText().toString());
					json.put("requestedPrice", requestedPrice.getText()
							.toString());
					json.put("date", currentDateTime);
					json.put("deadLine", dateDisplay.getText().toString());
				} catch (JSONException e) {
					e.printStackTrace();
				}

				PostRequest request = new PostRequest(Config.API_BASE_URL
						+ "/tangle/" + tangleID + "/request" + requestID
						+ "/offer") {
					protected void onPostExecute(String response) {
						if (this.getStatusCode() == 201) {
							// redirection
							// send notification
						} else if (this.getStatusCode() == 400) {
							// showErrorMessage();
						}
					}
				};
				request.addHeader(Config.API_SESSION_ID, sessionId);
				request.setBody(json);
				request.execute();

			}
		});
		pickDate.setOnClickListener(new View.OnClickListener() {
			public void onClick(View v) {
				showDialog(DATE_DIALOG_ID);
			}
		});
		updateDisplay();
	}

	/**
	 * this method update the display of the chosen deadLine
	 * 
	 * @param none
	 * @return none
	 * @author Salma Khaled
	 */

	private void updateDisplay() {
		dateDisplay.setError(null);
		this.dateDisplay.setText(new StringBuilder().append(deadLineDay)
				.append("/").append(deadLineMonth + 1).append("/")
				.append(deadLineYear).append(" "));
	}

	private DatePickerDialog.OnDateSetListener mDateSetListener = new DatePickerDialog.OnDateSetListener() {
		public void onDateSet(DatePicker view, int year, int monthOfYear,
				int dayOfMonth) {
			deadLineYear = year;
			deadLineMonth = monthOfYear;
			deadLineDay = dayOfMonth;
			boolean valid = isValidDeadLine();
			if (!valid) {
				Toast.makeText(getApplicationContext(),
						"The DeadLine can NOT be over already!!",
						Toast.LENGTH_SHORT).show();
				checkBox.setChecked(false);
				return;
			}
			updateDisplay();
		}
	};

	/**
	 * this method creates the dialog that u can choose the deadLine from
	 * 
	 * @param int id the id of the dialog
	 * @return Dialog datePickerDialog that is marked at deadLine date which is
	 *         set initially as today's date
	 * @author Salma Khaled
	 */
	protected Dialog onCreateDialog(int id) {
		switch (id) {
		case DATE_DIALOG_ID:
			return new DatePickerDialog(this, mDateSetListener, deadLineYear,
					deadLineMonth, deadLineDay);
		}
		return null;

	}

	/**
	 * check if the deadLine is valid and not in the past
	 * 
	 * @param none
	 * @return true if it is valid , false otherwise
	 * @author Salma Khaled
	 */
	private boolean isValidDeadLine() {
		if (currentYear > deadLineYear
				|| (currentYear == deadLineYear && currentMonth > deadLineMonth)
				|| (currentYear == deadLineYear
						&& currentMonth == deadLineMonth && currentDay > deadLineDay))
			return false;
		return true;
	}

	OnFocusChangeListener focusListener = new OnFocusChangeListener() {
		public void onFocusChange(View view, boolean hasFocus) {
			EditText editText = (EditText) view;
			if (!hasFocus) {
				if (isEmpty(editText)) {
					Post.setEnabled(false);
				}
			} else {
				checkBox.setChecked(false);
			}
		}
	};

	/**
	 * this method is called on clicking on the checkbox it checks whether the
	 * fields are empty or not and then accordingly either error messages will
	 * be set or Post button will be enabled
	 * 
	 * @param View
	 *            view which will be the checkbox
	 * @return none
	 * @author Salma Khaled
	 */
	public void itemClicked(View view) {
		View focusedView = getCurrentFocus();
		focusedView.clearFocus();
		CheckBox checkBox = (CheckBox) view;
		if (isEmpty(description) | isEmpty(requestedPrice)) {
			checkBox.setChecked(false);
		} else {
			checkBox.setChecked(true);
			Post.setEnabled(true);
		}
	}

	/**
	 * check if an editText is Empty
	 * 
	 * @param editText
	 * @return boolean true if that editText is empty and false otherwise and it
	 *         will set an error then
	 * @author Salma Khaled
	 */
	private boolean isEmpty(EditText editText) {
		if (editText.getText().toString().length() == 0) {
			editText.setError("This Field is Required");
			return true;
		}
		editText.setError(null);
		return false;
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
	//	getMenuInflater().inflate(R.menu.create_offer, menu);
		return true;
	}

}
