package com.megasoft.entangle;

import java.io.ByteArrayOutputStream;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.config.Config;
import com.megasoft.requests.PostRequest;

import android.util.Base64;
import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.BitmapDrawable;
import android.net.Uri;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

public class CreateTangleActivity extends Activity {

	private static final int REQUEST_CODE = 1;
	public static final int BUTTON_POSITIVE = 0xffffffff;
	public static final int GREEN = 0xff00ff00;
	public static final int RED = 0xffff0000;
	public static final int BLACK = 0xff000000;
	String encodedImage;
	String sessionId;
	SharedPreferences settings;

	private static Integer[] imageIconDatabase = { R.drawable.aatrox,
			R.drawable.ahri, R.drawable.akali, R.drawable.amumu,
			R.drawable.zac, R.drawable.ziggs };

	private String[] imageNameDatabase = { "aatrox", "ahri", "akali", "amumu",
			"zac", "ziggs" };

	private int usedIcon;

	@Override
	/**
	 * When the activity is created, it sets up listeners to the tangle name field.
	 * @param Bundle savedInstanceState
	 * @author Mansour
	 */
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_create_tangle);
		this.settings = getSharedPreferences(Config.SETTING, 0);
		this.sessionId = settings.getString(Config.SESSION_ID, "");
		Spinner iconSpinner = (Spinner) findViewById(R.id.iconSpinner);
		iconSpinner.setAdapter(new TangleIconSpinnerAdapter(
				CreateTangleActivity.this, R.layout.spinner_icons,
				imageNameDatabase));
		iconSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {

			@Override
			public void onItemSelected(AdapterView<?> arg0, View arg1,
					int arg2, long arg3) {
				usedIcon = arg0.getSelectedItemPosition();
			}

			@Override
			public void onNothingSelected(AdapterView<?> arg0) {
			}
		});
		usedIcon = iconSpinner.getSelectedItemPosition();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.create_tangle, menu);
		return true;
	}

	/**
	 * Redirects to the gallery when clicking choose icon button.
	 * 
	 * @param View
	 *            view
	 * @author Mansour
	 */
	public void chooseIcon(View view) {
		goToGallery();
	}

	/**
	 * Goes to the gallery to pick an image from there.
	 * 
	 * @author Mansour
	 */
	public void goToGallery() {
		startActivityForResult(new Intent(Intent.ACTION_PICK,
				android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI),
				REQUEST_CODE);
	}

	/**
	 * Waits for the image picked from the gallery and encodes it to a String.
	 * 
	 * @param int requestCode
	 * @param int resultCode
	 * @param Intent
	 *            data
	 * @author Mansour
	 */
	@SuppressLint("NewApi")
//	public void onActivityResult(int requestCode, int resultCode, Intent data) {
//		super.onActivityResult(requestCode, resultCode, data);
//		if (resultCode == RESULT_OK && requestCode == REQUEST_CODE
//				&& null != data) {
//			Bitmap bitmap = getPhotoPath(data.getData());
//			Button tangleIcon = (Button) findViewById(R.id.tangleIcon);
//			tangleIcon
//					.setBackground(new BitmapDrawable(getResources(), bitmap));
//			encodeToBase64(bitmap);
//		}
//	}

	public void encodeToBase64(Bitmap bitmap) {
		ByteArrayOutputStream baos = new ByteArrayOutputStream();
		bitmap.compress(Bitmap.CompressFormat.PNG, 100, baos);
		byte[] byteArray = baos.toByteArray();
		encodedImage = Base64.encodeToString(byteArray, Base64.DEFAULT);
	}

	/**
	 * Get the path of the photo the user picked from the gallery and returns
	 * the image as a bitmap.
	 * 
	 * @param uri
	 * @return Bitmap
	 * @author Mansour
	 */
	public Bitmap getPhotoPath(Uri uri) {
		String[] projection = { android.provider.MediaStore.Images.Media.DATA };
		Cursor cursor = getContentResolver().query(uri, projection, null, null,
				null);
		int columnIndex = cursor.getColumnIndexOrThrow(projection[0]);
		cursor.moveToFirst();
		String filePath = cursor.getString(columnIndex);
		cursor.close();
		Bitmap bitmap = BitmapFactory.decodeFile(filePath);

		return bitmap;
	}

	/**
	 * Creates the tangle when Create button is clicked.
	 * 
	 * @param view
	 * @author Mansour
	 */
	public void create(View view) {
		EditText tangleName = (EditText) findViewById(R.id.tangleName);
		EditText tangleDescription = (EditText) findViewById(R.id.tangleDescription);
		if (tangleName.getText().toString().equals("")) {
			Toast.makeText(getApplicationContext(), "Tangle Name Is Empty",
					Toast.LENGTH_LONG).show();
		} else {
			if (tangleName.getCurrentTextColor() == RED) {
				Toast.makeText(getApplicationContext(),
						"Tangle Name Unavailable", Toast.LENGTH_LONG).show();
			} else {
				if (tangleDescription.getText().toString().equals("")) {
					Toast.makeText(getApplicationContext(),
							"Descritpion Is Empty", Toast.LENGTH_LONG).show();
				} else {
					Bitmap bitmap = BitmapFactory.decodeResource(
							getResources(), imageIconDatabase[usedIcon]);
					encodeToBase64(bitmap);
					sendTangleToServer();
				}
			}
		}
	}

	/**
	 * Sends the tangle info to the server.
	 * 
	 * @author Mansour
	 */
	public void sendTangleToServer() {
		PostRequest imagePostRequest = new PostRequest(Config.API_BASE_URL
				+ "/tangle") {
			protected void onPostExecute(String response) {
				if (this.getStatusCode() == 200) {
					EditText tangleName = (EditText) findViewById(R.id.tangleName);
					tangleName.setError("Tangle Is Already Taken");
				} else {
					if (!(this.getStatusCode() == 201)) {
						Toast.makeText(getApplicationContext(),
								"Try Again Later" + this.getStatusCode(),
								Toast.LENGTH_LONG).show();
					} else {
						goToHomePage();
					}
				}
			}
		};
		JSONObject imageJSON = new JSONObject();
		try {
			imageJSON.put("tangleName",
					((EditText) findViewById(R.id.tangleName)).getText()
							.toString());
			imageJSON.put("tangleIcon", encodedImage);
		} catch (JSONException e) {
			e.printStackTrace();
		}
		imagePostRequest.setBody(imageJSON);
		imagePostRequest.addHeader(Config.API_SESSION_ID, sessionId);
		imagePostRequest.execute();
	}

	/**
	 * Shows a message dialogue to the user when called showing the message that
	 * is called with.
	 * 
	 * @param message
	 * @author Mansour
	 */
	public void showMessage(String message) {
		AlertDialog ad = new AlertDialog.Builder(this).create();
		ad.setCancelable(false);
		ad.setMessage(message);
		ad.setButton(BUTTON_POSITIVE, "OK",
				new DialogInterface.OnClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which) {
						dialog.dismiss();
					}
				});
		ad.show();
	}

	/**
	 * Resets a color of a text in a text field to black.
	 * 
	 * @param id
	 * @author Mansour
	 */
	public void resetColor(int id) {
		TextView textView = (TextView) findViewById(id);
		textView.setTextColor(BLACK);
	}

	/**
	 * Changes the color of a text inside a text field to green or red according
	 * to its availability.
	 * 
	 * @param available
	 * @param id
	 * @author Mansour
	 */
	public void insertAvailability(boolean available, int id) {

		TextView textView = (TextView) findViewById(id);
		if (available == true) {
			textView.setTextColor(GREEN);
		} else {
			textView.setTextColor(RED);
		}
	}

	/**
	 * Shows a dialogue informing that the tangle is created and redirecting to
	 * homepage when pressing OK.
	 * 
	 * @author Mansour
	 */
	public void goToHomePage() {
		Toast.makeText(getApplicationContext(), "Your Tangle Has Been Created",
				Toast.LENGTH_LONG).show();
		goToHomeHelper();
	}

	/**
	 * Redirects the user to the home page.
	 * 
	 * @author Mansour
	 */
	public void goToHomeHelper() {
		this.finish();
	}

	/**
	 * Returns to the parent activity when pressing the cancel button.
	 * 
	 * @param view
	 * @author Mansour
	 */
	public void cancelRedirect(View view) {
		this.finish();
	}

	private class TangleIconSpinnerAdapter extends ArrayAdapter<String> {

		public TangleIconSpinnerAdapter(Context context,
				int textViewResourceId, String[] objects) {
			super(context, textViewResourceId, objects);
		}

		@Override
		public View getDropDownView(int position, View convertView,
				ViewGroup parent) {
			return getCustomView(position, convertView, parent);
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent) {
			return getCustomView(position, convertView, parent);
		}

		public View getCustomView(int position, View convertView,
				ViewGroup parent) {

			LayoutInflater inflater = getLayoutInflater();
			View row = inflater.inflate(R.layout.spinner_icons, parent, false);

			ImageView icon = (ImageView) row.findViewById(R.id.icon);
			icon.setImageResource(imageIconDatabase[position]);

			return row;
		}
	}
}
