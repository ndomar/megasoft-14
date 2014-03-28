package com.example.editinfo;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.EditText;

public class AddEmail extends Activity {

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		getIntent();
		setContentView(R.layout.add_mail);

	}

	public void SaveNewEmail(View view) {
		EditText addedMail = (EditText) findViewById(R.id.AddedMail);
		String text = addedMail.getText().toString();
		Log.i("tag", text);
		Intent editDes = new Intent(this, MyInfo.class);
		editDes.putExtra("newMail", text);
		startActivity(editDes);
	}
}
