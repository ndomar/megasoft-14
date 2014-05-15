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
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
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
	 * the post Button that will send the server the postrequest to Post the
	 * offer
	 */
	Button post;
	/**
	 * 
	 * cancel button cancel request
	 */
	Button cancel;
	/**
	 * the json object to be sent in the postrequest
	 */
	JSONObject json = new JSONObject();
	/**
	 * the chosen deadLine year
	 * 
	 */
	int deadLineYear;
	/**
	 * the chosen deadLine month
	 * 
	 */
	int deadLineMonth;
	/**
	 * the chosen deadLine day
	 * 
	 */
	int deadLineDay;
	/**
	 * Button clicked to pick deadLine date
	 */
	Button pickDate;
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
	final String date = currentDay + "-" + (currentMonth + 1) + "-"
			+ currentYear;

	/**
	 * this activity
	 */
	final Activity self = this;
	/**
	 * a flag to indicate whether the user chose a date or not
	 */
	boolean dateIsSet;
	/**
	 * a TextView to tell the user there is some error with the deadline
	 */
	TextView deadlineError;

	/**
	 * on creation of the activity it takes data from the fields and send it as
	 * json object on clicking the Post Button
	 * 
	 * @param Bundle
	 *            savedInstanceState
	 * @return none
	 * @author Salma Khaled
	 */
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		getActionBar().hide();
		Intent previousIntent = getIntent();
		final int tangleId = previousIntent.getIntExtra("tangleId", 0);
		final int requestId = previousIntent.getIntExtra("requestId", 0);
		settings = getSharedPreferences(Config.SETTING, 0);
		sessionId = settings.getString(Config.SESSION_ID, "");
		setContentView(R.layout.activity_create_offer);
		description = (EditText) findViewById(R.id.description);
		requestedPrice = (EditText) findViewById(R.id.price);
		post = (Button) findViewById(R.id.post);
		cancel = (Button) findViewById(R.id.cancelRequest);
		pickDate = (Button) findViewById(R.id.myDatePickerButton);
		pickDate.setText("Due date");
		deadLineYear = calendar.get(Calendar.YEAR);
		deadLineMonth = calendar.get(Calendar.MONTH);
		deadLineDay = calendar.get(Calendar.DAY_OF_MONTH);
		dateIsSet = false;
		deadlineError = (TextView) findViewById(R.id.deadlineError);
		deadlineError.setVisibility(View.GONE);
		final String currentDateTime = date + " " + calendar.get(Calendar.HOUR)
				+ ":" + calendar.get(Calendar.MINUTE) + ":"
				+ calendar.get(Calendar.SECOND);
		cancel.setOnClickListener(new View.OnClickListener() {

			public void onClick(View arg0) {
				finish();
			}
		});

		post.setOnClickListener(new View.OnClickListener() {

			public void onClick(View arg0) {
				if (isEmpty(description) | isEmpty(requestedPrice)) {
					return;
				}

				if (pickDate.getText().toString().equals("Due Date")) {
					deadlineError.setText("This field is required");
					deadlineError.setVisibility(View.VISIBLE);
					return;
				}
				deadlineError.setVisibility(View.GONE);
				try {
					json.put("description", description.getText().toString());
					json.put("requestedPrice", requestedPrice.getText()
							.toString());
					json.put("date", currentDateTime);
					json.put("deadLine", pickDate.getText().toString());
				} catch (JSONException e) {
					e.printStackTrace();
				}

				PostRequest request = new PostRequest(Config.API_BASE_URL
						+ "/tangle/" + tangleId + "/request/" + requestId
						+ "/offer") {
					protected void onPostExecute(String response) {
						if (this.getStatusCode() == 201) {
							Intent intent = new Intent(self,
									RequestActivity.class);
							intent.putExtra("tangleId", tangleId);
							intent.putExtra("requestId", requestId);
							startActivity(intent);
							finish();
						} else {

							Toast.makeText(getApplicationContext(),
									this.getErrorMessage(),
									Toast.LENGTH_SHORT).show();
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
				dateIsSet = true;
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
		pickDate.setError(null);
		if (!dateIsSet) {
			this.pickDate.setText("Due Date");
		} else {
			this.pickDate.setText(new StringBuilder().append(deadLineDay)
					.append("-").append(deadLineMonth + 1).append("-")
					.append(deadLineYear).append(" "));
		}
	}

	private DatePickerDialog.OnDateSetListener mDateSetListener = new DatePickerDialog.OnDateSetListener() {
		public void onDateSet(DatePicker view, int year, int monthOfYear,
				int dayOfMonth) {
			deadLineYear = year;
			deadLineMonth = monthOfYear;
			deadLineDay = dayOfMonth;
			boolean valid = isValidDeadLine();
			if (!valid) {
				deadlineError.setText("Ops! deadline has passed");
				deadlineError.setVisibility(View.VISIBLE);
				return;
			}
			deadlineError.setVisibility(View.GONE);
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
			editText.setError("This field is required");
			return true;
		}
		editText.setError(null);
		return false;
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		// getMenuInflater().inflate(R.menu.create_offer, menu);
		return true;
	}

}
