package com.megasoft.entangle;

import java.util.Calendar;

import org.json.JSONObject;

import com.megasoft.config.Config;

import android.os.Bundle;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.view.Menu;
import android.view.View;
import android.view.View.OnFocusChangeListener;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.TextView;

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
	
	
	       public void itemClicked(View view)  {
	    	   View focusedView = getCurrentFocus();
	   		focusedView.clearFocus();
	    	   CheckBox checkBox = (CheckBox) view;
			if(isEmpty(description) | isEmpty(requestedPrice)){
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
