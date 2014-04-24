package com.megasoft.notifications;

import com.megasoft.entangle.R;

import android.app.Activity;
import android.net.Uri;
import android.os.Bundle;
import android.widget.TextView;

public class NotificationTwo extends Activity {

	@Override
	public void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);

		setContentView(R.layout.notification_two);

		String output = "Inside the activity of Notification two: ";

		TextView dataIntent = (TextView) findViewById(R.id.text2);

		// take the data and the extras of the intent

		Uri url = getIntent().getData();

		Bundle extras = getIntent().getExtras();

		output = output + url.toString();

		// if there are extras, add them to the output string

		if (extras != null) {

			output = output + " from " + extras.getString("from");

		}

		dataIntent.setText(output);

	}

}
