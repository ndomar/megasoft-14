package com.megasoft.entangle;

import android.os.Bundle;
import android.app.Activity;
import android.view.Menu;

public class RequestActivity extends Activity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_request);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		getMenuInflater().inflate(R.menu.request_information, menu);
		return true;
	}

}
