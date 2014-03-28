package com.example.editinfo;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Spinner;

public class EditAge extends Activity {
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		getIntent();
		setContentView(R.layout.edit_age);
	}

	public void SaveNewAge(View view) {
		Log.i("tagblo2","Ana geeeet");
		Spinner newday = (Spinner) findViewById(R.id.days);
		Log.i("tagblo2","Ana geeeet tani");
		Spinner newmonths = (Spinner) findViewById(R.id.months);
		Log.i("tagblo2","Ana geeeet talt");
		Spinner newyear = (Spinner) findViewById(R.id.years);
		Log.i("tagblo2","Ana geeeet rab3");
		String text = newday.getSelectedItem().toString() + newmonths.getSelectedItem().toString()
				+ newyear.getSelectedItem().toString();
		Log.i("tag", text);
		Intent editDes = new Intent(this, MyInfo.class);
		editDes.putExtra("Age", text);
		startActivity(editDes);

	}
}
