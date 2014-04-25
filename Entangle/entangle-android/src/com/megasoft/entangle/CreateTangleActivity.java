package com.megasoft.entangle;


import java.io.ByteArrayOutputStream;

import org.json.JSONException;
import org.json.JSONObject;

import com.megasoft.requests.GetRequest;
import com.megasoft.requests.PostRequest;

import android.text.Editable;
import android.text.TextWatcher;
import android.util.Base64;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;

public class CreateTangleActivity extends Activity {

	private static final int REQUEST_CODE = 1;
	public static final int BUTTON_POSITIVE = 0xffffffff;
	public static final int GREEN = 0xff00ff00;
	public static final int RED = 0xffff0000;
	public static final int BLACK = 0xff000000;
	String encodedImage;

	@Override
	/**
	 * When the activity is created, it sets up listeners to the tangle name field
	 * @param Bundle savedInstanceState
	 * @author Mansour
	 */
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_create_tangle);

		EditText tangleName = (EditText) findViewById(R.id.tangleName);
		tangleName.addTextChangedListener(new TextWatcher() {

			@Override
			public void afterTextChanged(Editable arg0) {
				resetColor(R.id.tangleName);
			}

			@Override
			public void beforeTextChanged(CharSequence s, int start, int count,
					int after) {
			}

			@Override
			public void onTextChanged(CharSequence s, int start, int before,
					int count) {
			}
		});

	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {

		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.create_tangle, menu);
		return true;
	}

	/**
	 * Checks if the tangle name already exists or not
	 * 
	 * @param view
	 * @author Mansour
	 */
	public void checkTangleName(View view) {
		String tangleNameText = ((EditText) findViewById(R.id.tangleName))
				.getText().toString();
		if ((tangleNameText.split(" ")).length > 1) {
			showMessage("TANGLE NAME SHOULD BE ONE WORD");
		} else {
			GetRequest getNameRequest = new GetRequest(
					"http://entangle2.apiary-mock.com/tangle/check/"
							+ tangleNameText) {
				protected void onPostExecute(String response) {
					if (!(this.getStatusCode() == 404)) {
						insertAvailability(false, R.id.tangleName);
					} else {
						insertAvailability(true, R.id.tangleName);
					}
				}
			};
			getNameRequest.addHeader("X-SESSION-ID", "fdgdf");
			getNameRequest.execute();
		}
	}

	/**
	 * Redirects to gallery when clicking choose icon button
	 * 
	 * @param view
	 * @author Mansour
	 */
	public void chooseIcon(View view) {
		goToGallery();
	}

	/**
	 * Goes to the gallery to pick an image from there
	 */
	public void goToGallery() {
		startActivityForResult(new Intent(Intent.ACTION_PICK,
				android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI),
				REQUEST_CODE);
	}

	/**
	 * Waits for the image picked from the gallery and encodes it to a string
	 * 
	 * @param int requestCode
	 * @param int resultCode
	 * @param Intent data
	 * @author Mansour
	 */
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		if (resultCode == RESULT_OK && requestCode == REQUEST_CODE
				&& null != data) {
			Bitmap bitmap = getPhotoPath(data.getData());
			ImageView imageView = (ImageView) findViewById(R.id.icon);
			imageView.setImageBitmap(bitmap);
			ByteArrayOutputStream baos = new ByteArrayOutputStream();
			bitmap.compress(Bitmap.CompressFormat.PNG, 100, baos);
			byte[] byteArray = baos.toByteArray();
			encodedImage = Base64.encodeToString(byteArray, Base64.DEFAULT);
		}
	}

	/**
	 * Get the path of the photo the user picked from the gallery and returns
	 * the image as a bitmap
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
	 * Creates the tangle when Create button is clicked
	 * 
	 * @param view
	 * @author Mansour
	 */
	public void create(View view) {
		EditText tangleName = (EditText) findViewById(R.id.tangleName);
		ImageView tangleIcon = (ImageView) findViewById(R.id.icon);
		if ((tangleName.getText().toString()).equals("")) {
			showMessage("PLEASE ENTER A TANGLE NAME");
		} else {
			if (tangleName.getCurrentTextColor() == BLACK) {
				showMessage("PLEASE CHECK IF THE TANGLE NAME IS AVAILABLE FIRST");
			} else {
				if (tangleName.getCurrentTextColor() == RED) {
					showMessage("TANGLE NAME UNAVAILABLE, PLEASE CHOOSE ANOTHER NAME");
				} else {
					if (tangleIcon.getDrawable() == null) {
						showMessage("PLEASE CHOOSE A TANGLE ICON FIRST");
					} else {
						sendTangleToServer();
					}
				}
			}
		}
	}

	/**
	 * Sends the tangle info to the server
	 * @author Mansour
	 */
	public void sendTangleToServer() {
		PostRequest imagePostRequest = new PostRequest(
				"http://entangle2.apiary-mock.com/tangle") {
			protected void onPostExecute(String response) {
				if (!(this.getStatusCode() == 201)) {
					showMessage("ERROR, TRY AGAIN LATER");
				} else {
					goToHomePage();
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
		imagePostRequest.addHeader("X-SESSION-ID", "fgdzr");
		imagePostRequest.execute();
	}

	/**
	 * Shows a message dialogue to the user when called showing the message that
	 * is called with
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
	 * Resets a color of a text in a text field to black
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
	 * to its availability
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
	 * homepage when pressing OK
	 * @author Mansour
	 */
	public void goToHomePage() {
		AlertDialog ad = new AlertDialog.Builder(this).create();
		ad.setCancelable(false);
		ad.setMessage("CONGRATULATIONS, YOUR TANGLE IS CREATED");
		ad.setButton(BUTTON_POSITIVE, "OK",
				new DialogInterface.OnClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which) {
						dialog.dismiss();
						goToHomeHelper();
					}
				});
		ad.show();
	}

	/**
	 * Redirects the user to the home page
	 * @author Mansour
	 */
	public void goToHomeHelper() {
		startActivity(new Intent(this, MainActivity.class));
	}
}
