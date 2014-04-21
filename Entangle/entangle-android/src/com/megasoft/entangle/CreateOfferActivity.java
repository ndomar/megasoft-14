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

	SharedPreferences settings;
	String sessionId;
	EditText description;
	EditText requestedPrice;
	Button Post;
	JSONObject json = new JSONObject();

	int deadLineYear;

	int deadLineMonth;

	int deadLineDay;

	TextView dateDisplay;

	Button pickDate;
	CheckBox checkBox;

	static final int DATE_DIALOG_ID = 0;

	final Calendar calendar = Calendar.getInstance();

	final int currentDay = calendar.get(Calendar.DAY_OF_MONTH);

	final int currentMonth = calendar.get(Calendar.MONTH);

	final int currentYear = calendar.get(Calendar.YEAR);

	final String date = currentDay + "/" + (currentMonth + 1) + "/"
			+ currentYear;

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

	protected Dialog onCreateDialog(int id) {
		switch (id) {
		case DATE_DIALOG_ID:
			return new DatePickerDialog(this, mDateSetListener, deadLineYear,
					deadLineMonth, deadLineDay);
		}
		return null;

	}

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
		getMenuInflater().inflate(R.menu.create_offer, menu);
		return true;
	}

}
