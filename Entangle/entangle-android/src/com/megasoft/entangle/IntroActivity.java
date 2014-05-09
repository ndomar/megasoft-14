package com.megasoft.entangle;


import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;

public class IntroActivity extends Activity{

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_intro);
		getActionBar().hide();
	}

	/**
	 * This method redirects the user to create a tangle activtiy
	 * @param view
	 * @author Salma Amr
	 */
	public void redirectToCreateTangle(View view) {
		startActivity(new Intent(this, CreateTangleActivity.class));
		this.finish();
	}
}