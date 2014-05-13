package com.megasoft.entangle;

import java.util.Calendar;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

public class CreateRequestActivity extends Activity {
	/**
	 * the Post Button that will send the server the postrequest to Post the
	 * Request to the stream
	 */
	Button post;
	/**
	 * 
	 * cancel button cancel request
	 */
	Button cancel;
	/**
	 * EditText that will contain the Request's description
	 */
	EditText description;
	/**
	 * EditText that will contain the Request's requested price
	 */
	EditText requestedPrice;
	/**
	 * EditText that will contain the tags
	 */
	EditText tags;
	/**
	 * the json object to be sent in the postrequest
	 */
	JSONObject json = new JSONObject();
	/**
	 * the chosen deadLine year
	 */
	int deadLineYear;
	/**
	 * the chosen deadLine month
	 */
	int deadLineMonth;
	/**
	 * the chosen deadLine day
	 */
	int deadLineDay;
	/**
	 * Button clicked to pick deadLine date
	 */
	Button pickDate;
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
	 * the date dialog Id
	 */
	static final int DATE_DIALOG_ID = 0;
	/**
	 * json array to store the tags
	 */
	JSONArray jsonTagsArray;
	/**
	 * array to store the tags each as a string
	 */
	String[] tagsArray;
	/**
	 * sessionId of the currently logged in user
	 */
	String sessionId;
	/**
	 * preference instance
	 */
	SharedPreferences settings;
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
		settings = getSharedPreferences(Config.SETTING, 0);
		sessionId = settings.getString(Config.SESSION_ID, "");
		setContentView(R.layout.activity_create_request);
		description = (EditText) findViewById(R.id.description);
		requestedPrice = (EditText) findViewById(R.id.price);
		tags = (EditText) findViewById(R.id.tags);
		post = (Button) findViewById(R.id.post);
		cancel = (Button) findViewById(R.id.cancelRequest);
		pickDate = (Button) findViewById(R.id.myDatePickerButton);
		deadLineYear = calendar.get(Calendar.YEAR);
		deadLineMonth = calendar.get(Calendar.MONTH);
		deadLineDay = calendar.get(Calendar.DAY_OF_MONTH);
		jsonTagsArray = new JSONArray();
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
				if (isEmpty(description)) {
					return;
				}
				if (!tags.getText().toString().equals("")) {
					tagsArray = tags.getText().toString().split(",");
					for (int i = 0; i < tagsArray.length; i++) {
						try {
							jsonTagsArray.put(i, tagsArray[i]);
						} catch (JSONException e) {
							e.printStackTrace();
						}
					}
				}

				try {
					json.put("description", description.getText().toString());
					json.put("requestedPrice", requestedPrice.getText()
							.toString());
					json.put("date", currentDateTime);
					String deadLineData = pickDate.getText().toString();
					if (deadLineData.equals("Due Date")) {
						deadLineData = "";
					}
					json.put("deadLine", deadLineData);
					json.put("tags", jsonTagsArray);
				} catch (JSONException e) {
					e.printStackTrace();
				}

				PostRequest request = new PostRequest(
						Config.API_BASE_URL_SERVER + "/tangle/" + tangleId
								+ "/request") {
					protected void onPostExecute(String response) {
						if (this.getStatusCode() == 201) {
							finish();
						} else {
							Toast.makeText(getApplicationContext(),
									"Error, Can not create request",
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
	 * this method shows the dialog that u can choose the deadLine from
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

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		// getMenuInflater().inflate(R.menu.requests, menu);
		return true;
	}

}