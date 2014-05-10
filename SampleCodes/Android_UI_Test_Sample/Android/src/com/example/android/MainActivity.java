package com.example.android;

import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.widget.TextView;

public class MainActivity extends ActionBarActivity {

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
	}

	/**
	 * This method defines the functionality of the button in my activity, 
	 * on clicking the button it just makes the textView visible 
	 * @param view
	 * @author Salma Amr
	 */
	public void hallo(View view) {
		TextView textView = (TextView) findViewById(R.id.textView1);
		textView.setVisibility(View.VISIBLE);
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
		return true;
	}

}
