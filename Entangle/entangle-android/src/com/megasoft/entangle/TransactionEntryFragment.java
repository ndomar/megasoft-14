package com.megasoft.entangle;

import android.support.v4.app.Fragment;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

public class TransactionEntryFragment extends Fragment {
	
	private View view;

	/**
	 * Requester name
	 */
	private String requester;
	
	/**
	 * Request description
	 */
	private String request;
	
	/**
	 * Transaction amount
	 */
	private int amount;
	
	/**
	 * Sets the value of the requester name
	 * @param requester
	 */
	public void setRequester(String requester) {
		this.requester = requester;
	}

	/**
	 * Sets the value of the request description
	 * @param request
	 */
	public void setRequest(String request) {
		this.request = request;
	}

	/**
	 * Sets the value of the transaction amount
	 * @param amount
	 */
	public void setAmount(int amount) {
		this.amount = amount;
	}

	
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstancState){
		this.view = inflater.inflate(R.layout.fragment_transaction_entry, container,false);
		((TextView)view.findViewById(R.id.request_description)).setText(request);
		((TextView)view.findViewById(R.id.transaction_amount)).setText(""+amount);
		((TextView)view.findViewById(R.id.requester)).setText(requester);
		return view;
	}
	
}
