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
import android.view.Menu;
import android.view.View;
import android.view.View.OnFocusChangeListener;
import android.widget.Button;
import android.widget.CheckBox;
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
	Button Post;
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
	 * CheckBox that the user should check after filling the fields
	 */
	CheckBox checkBox;
	/**
	 * flag that will indicate if the checkbox was checked but the user went
	 * back to change or fill another field
	 */
	boolean flag;
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
	 * the chosen deadLine month , in case user didn't choose a value it will
	 * be set to current month
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
	 * on creation of the activity it takes data from the fields and send it as
	 * json object on clicking the Post Button
	 * 
	 * @author Salma Khaled
	 */
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		Intent previousIntent = getIntent();
		final int tangleID = previousIntent.getIntExtra("tangleID", 0);
		settings = getSharedPreferences(Config.SETTING, 0);
		sessionId = settings.getString(Config.SESSION_ID, "");
		setContentView(R.layout.activity_create_request);
		description = (EditText) findViewById(R.id.description);
		requestedPrice = (EditText) findViewById(R.id.price);
		tags = (EditText) findViewById(R.id.tags);
		Post = (Button) findViewById(R.id.post);
		checkBox = (CheckBox) findViewById(R.id.checkBox);
		Post.setEnabled(false);
		description.setOnFocusChangeListener(focusListener);
		requestedPrice.setOnFocusChangeListener(focusListener);
		tags.setOnFocusChangeListener(focusListener);
		dateDisplay = (TextView) findViewById(R.id.showMyDate);
		pickDate = (Button) findViewById(R.id.myDatePickerButton);
		deadLineYear = calendar.get(Calendar.YEAR);
		deadLineMonth = calendar.get(Calendar.MONTH);
		deadLineDay = calendar.get(Calendar.DAY_OF_MONTH);
		jsonTagsArray = new JSONArray();
		final String currentDateTime = date + " " + calendar.get(Calendar.HOUR)
				+ ":" + calendar.get(Calendar.MINUTE) + ":"
				+ calendar.get(Calendar.SECOND);
		Post.setOnClickListener(new View.OnClickListener() {

			public void onClick(View arg0) {
				tagsArray = tags.getText().toString().split(",");
				for (int i = 0; i < tagsArray.length; i++) {
					try {
						jsonTagsArray.put(i, tagsArray[i]);
					} catch (JSONException e) {
						e.printStackTrace();
					}
				}

				try {
					json.put("description", description.getText().toString());
					json.put("requestedPrice", requestedPrice.getText()
							.toString());
					json.put("date", currentDateTime);
					json.put("deadLine", dateDisplay.getText().toString());
					json.put("tags", jsonTagsArray);
				} catch (JSONException e) {
					e.printStackTrace();
				}

				PostRequest request = new PostRequest(Config.API_BASE_URL
						+ "/tangle/" + tangleID + "/request") {
					protected void onPostExecute(String response) {
						if (this.getStatusCode() == 201) {
							// redirection
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

	OnFocusChangeListener focusListener = new OnFocusChangeListener() {
		public void onFocusChange(View view, boolean hasFocus) {
			EditText editText = (EditText) view;
			if (!hasFocus) {
				if (isEmpty(editText)) {
					Post.setEnabled(false);
				}
			} else {
				if (!flag) {
					flag = true;
					checkBox.setChecked(false);
				}
			}
		}
	};

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
	 * this method checks if there is no error messages set, and if the checkbox
	 * is checked and only then it enables the Post button
	 * 
	 * @author Salma Khaled
	 */
	private void enablePostButton() {
		if (description.getError() == null && requestedPrice.getError() == null
				&& tags.getError() == null && checkBox.isChecked()) {
			Post.setEnabled(true);
		}
	}

	/**
	 * this method takes a view which will be the checkbox then clear focus from
	 * other views if the fields are empty it unchecks the checkedbox it calls
	 * enablePostButton() to check if we can enable the post button and then
	 * take the action of enabling it or setting error messages
	 * 
	 * @param view
	 *            which will be the checkbox
	 * @author Salma Khaled
	 */
	public void itemClicked(View view) {
		View focusedView = getCurrentFocus();
		focusedView.clearFocus();
		CheckBox checkBox = (CheckBox) view;
		if (checkBox.isChecked()) {
			if (!fieldsNotEmpty()) {
				checkBox.setChecked(false);
			}
			flag = false;
			enablePostButton();
		}
	}

	/**
	 * check if all the fields required are not empty
	 * 
	 * @return boolean true if they are all not empty false otherwise
	 * @author Salma Khaled
	 */
	private boolean fieldsNotEmpty() {
		if (!isEmpty(description) & !isEmpty(requestedPrice) & !isEmpty(tags))
			return true;
		return false;
	}

	/**
	 * check if the deadLine is valid and not in the past
	 * 
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